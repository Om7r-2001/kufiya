<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    // قائمة المشاريع المفتوحة
    public function index(Request $request)
    {
        $query = Project::query()
            ->whereIn('status', ['open', 'in_progress', 'completed'])
            ->with('owner');

        if ($search = $request->input('q')) {
            $query->where('title', 'like', '%' . $search . '%');
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        $projects = $query->latest()->paginate(12);

        return view('projects.index', compact('projects'));
    }

    // صفحة تفاصيل مشروع
    public function show(Project $project)
    {
        // زيادة عدد المشاهدات
        $project->increment('views_count');

        $project->load([
            'owner',
            'bids.seller',
            'selectedBid.seller',
        ]);

        $user = Auth::user();
        $userBid = null;

        if ($user) {
            $userBid = $project->bids()
                ->where('user_id', $user->id)
                ->first();
        }

        return view('projects.show', compact('project', 'user', 'userBid'));
    }

    // عرض نموذج إنشاء مشروع جديد (للمشتري)
    public function create()
    {
        $user = Auth::user();

        // تأكد أنه ليس seller فقط (يمكنك تعديل الشرط حسب نظام الأدوار لديك)
        if ($user->role !== 'buyer' && $user->role !== 'both') {
            abort(403, 'فقط المشتري يمكنه إنشاء مشروع جديد.');
        }

        return view('projects.create');
    }

    // حفظ مشروع جديد
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'buyer' && $user->role !== 'both') {
            abort(403, 'فقط المشتري يمكنه إنشاء مشروع جديد.');
        }

        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'category'      => 'nullable|string|max:255',
            'budget_min'    => 'nullable|integer|min:0',
            'budget_max'    => 'nullable|integer|min:0',
            'delivery_days' => 'nullable|integer|min:1',
            'description'   => 'required|string',
            'image'         => 'nullable|image|mimes:jpeg,jpg,png,webp|max:10240',
        ]);

        $data['user_id'] = $user->id;

        // معالجة الصورة إن وُجدت
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('projects', 'public');
            $data['image_path'] = $path;
        }

        // لو لم يتم إدخال budget_max نضعها بنفس budget_min
        if (!empty($data['budget_min']) && empty($data['budget_max'])) {
            $data['budget_max'] = $data['budget_min'];
        }

        $project = Project::create($data);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'تم إنشاء المشروع بنجاح وهو الآن مفتوح لاستقبال العروض.');
    }

    // صفحة "مشاريعي" للمشتري
    public function myProjects()
    {
        $user = Auth::user();

        // السماح للمشتري فقط (عدّل الأدوار حسب مشروعك)
        if ($user->role !== 'buyer' && $user->role !== 'both') {
            abort(403, 'هذه الصفحة متاحة للمشتري فقط.');
        }

        $projects = Project::where('user_id', $user->id)
            ->withCount('bids')
            ->latest()
            ->paginate(12);

        return view('projects.my', compact('projects'));
    }

        public function edit(Project $project)
    {
        $user = Auth::user();

        // صاحب المشروع فقط
        if ($project->user_id !== $user->id) {
            abort(403, 'غير مسموح لك بتعديل هذا المشروع.');
        }

        // نسمح بالتعديل فقط إذا المشروع ما زال "مفتوح"
        if ($project->status !== 'open') {
            return redirect()
                ->route('projects.show', $project)
                ->with('error', 'لا يمكن تعديل مشروع بعد بدء تنفيذه أو اكتماله.');
        }

        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $user = Auth::user();

        if ($project->user_id !== $user->id) {
            abort(403, 'غير مسموح لك بتعديل هذا المشروع.');
        }

        if ($project->status !== 'open') {
            return redirect()
                ->route('projects.show', $project)
                ->with('error', 'لا يمكن تعديل مشروع بعد بدء تنفيذه أو اكتماله.');
        }

        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'category'      => 'nullable|string|max:255',
            'budget_min'    => 'nullable|integer|min:0',
            'budget_max'    => 'nullable|integer|min:0',
            'delivery_days' => 'nullable|integer|min:1',
            'description'   => 'required|string',
            'image'         => 'nullable|image|mimes:jpeg,jpg,png,webp|max:10240',
        ]);

        if (!empty($data['budget_min']) && empty($data['budget_max'])) {
            $data['budget_max'] = $data['budget_min'];
        }

        // لو تم رفع صورة جديدة نحذف القديمة (اختياري) ونخزن الجديدة
        if ($request->hasFile('image')) {
            if ($project->image_path) {
                Storage::disk('public')->delete($project->image_path);
            }

            $path = $request->file('image')->store('projects', 'public');
            $data['image_path'] = $path;
        }

        $project->update($data);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'تم تحديث بيانات المشروع بنجاح.');
    }

    public function destroy(Project $project)
    {
        $user = Auth::user();

        if ($project->user_id !== $user->id) {
            abort(403, 'غير مسموح لك بحذف هذا المشروع.');
        }

        // قرار منطقي: لا نحذف مشروع لديه طلب فعلي قيد التنفيذ أو مكتمل
        if (in_array($project->status, ['in_progress', 'completed'])) {
            return redirect()
                ->route('projects.show', $project)
                ->with('error', 'لا يمكن حذف مشروع قيد التنفيذ أو مكتمل. يمكنك إلغاؤه فقط.');
        }

        $project->delete();

        return redirect()
            ->route('projects.my')
            ->with('success', 'تم حذف المشروع بنجاح.');
    }

}
