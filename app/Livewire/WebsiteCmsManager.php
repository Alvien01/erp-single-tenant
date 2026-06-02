<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CmsPage;
use App\Models\BlogPost;
use App\Models\Appointment;
use App\Models\Event;
use App\Models\EventAttendee;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\Customer;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class WebsiteCmsManager extends Component
{
    use WithPagination;

    public $search = '';
    public $activeTab = 'pages'; // pages, blog, appointments, events, elearning

    // CMS Page fields
    public $page_id, $page_title, $page_slug, $page_content, $page_meta_title, $page_meta_description, $page_status = 'draft', $page_sort_order = 0;

    // Blog fields
    public $blog_id, $blog_title, $blog_slug, $blog_content, $blog_excerpt, $blog_category, $blog_tags, $blog_status = 'draft';

    // Appointment fields
    public $apt_id, $apt_title, $apt_description, $apt_customer_id, $apt_assigned_to, $apt_start_time, $apt_end_time, $apt_location, $apt_status = 'scheduled', $apt_type = 'meeting', $apt_notes;

    // Event fields
    public $evt_id, $evt_name, $evt_description, $evt_venue, $evt_start_date, $evt_end_date, $evt_type = 'seminar', $evt_status = 'draft', $evt_max_attendees, $evt_ticket_price = 0;

    // Course fields
    public $crs_id, $crs_title, $crs_slug, $crs_description, $crs_category, $crs_level = 'beginner', $crs_status = 'draft', $crs_duration_hours = 0;

    public $modalType = null;
    public $isEdit = false;

    public function updatingSearch() { $this->resetPage(); }
    public function closeModal() { $this->modalType = null; $this->isEdit = false; }

    public function openModal($type, $id = null)
    {
        $this->modalType = $type; $this->isEdit = (bool) $id;
        match($type) {
            'page' => $id ? $this->loadPage($id) : $this->resetPageFields(),
            'blog' => $id ? $this->loadBlog($id) : $this->resetBlogFields(),
            'appointment' => $id ? $this->loadAppointment($id) : $this->resetAppointmentFields(),
            'event' => $id ? $this->loadEvent($id) : $this->resetEventFields(),
            'course' => $id ? $this->loadCourse($id) : $this->resetCourseFields(),
            default => null,
        };
    }

    private function loadPage($id) { $p = CmsPage::findOrFail($id); $this->page_id=$p->id; $this->page_title=$p->title; $this->page_slug=$p->slug; $this->page_content=$p->content; $this->page_meta_title=$p->meta_title; $this->page_meta_description=$p->meta_description; $this->page_status=$p->status; $this->page_sort_order=$p->sort_order; }
    private function resetPageFields() { $this->page_id=null; $this->page_title=''; $this->page_slug=''; $this->page_content=''; $this->page_meta_title=''; $this->page_meta_description=''; $this->page_status='draft'; $this->page_sort_order=0; }

    private function loadBlog($id) { $b = BlogPost::findOrFail($id); $this->blog_id=$b->id; $this->blog_title=$b->title; $this->blog_slug=$b->slug; $this->blog_content=$b->content; $this->blog_excerpt=$b->excerpt; $this->blog_category=$b->category; $this->blog_tags=$b->tags; $this->blog_status=$b->status; }
    private function resetBlogFields() { $this->blog_id=null; $this->blog_title=''; $this->blog_slug=''; $this->blog_content=''; $this->blog_excerpt=''; $this->blog_category=''; $this->blog_tags=''; $this->blog_status='draft'; }

    private function loadAppointment($id) { $a = Appointment::findOrFail($id); $this->apt_id=$a->id; $this->apt_title=$a->title; $this->apt_description=$a->description; $this->apt_customer_id=$a->customer_id; $this->apt_assigned_to=$a->assigned_to; $this->apt_start_time=$a->start_time?->format('Y-m-d\TH:i'); $this->apt_end_time=$a->end_time?->format('Y-m-d\TH:i'); $this->apt_location=$a->location; $this->apt_status=$a->status; $this->apt_type=$a->type; $this->apt_notes=$a->notes; }
    private function resetAppointmentFields() { $this->apt_id=null; $this->apt_title=''; $this->apt_description=''; $this->apt_customer_id=''; $this->apt_assigned_to=''; $this->apt_start_time=''; $this->apt_end_time=''; $this->apt_location=''; $this->apt_status='scheduled'; $this->apt_type='meeting'; $this->apt_notes=''; }

    private function loadEvent($id) { $e = Event::findOrFail($id); $this->evt_id=$e->id; $this->evt_name=$e->name; $this->evt_description=$e->description; $this->evt_venue=$e->venue; $this->evt_start_date=$e->start_date?->format('Y-m-d\TH:i'); $this->evt_end_date=$e->end_date?->format('Y-m-d\TH:i'); $this->evt_type=$e->type; $this->evt_status=$e->status; $this->evt_max_attendees=$e->max_attendees; $this->evt_ticket_price=$e->ticket_price; }
    private function resetEventFields() { $this->evt_id=null; $this->evt_name=''; $this->evt_description=''; $this->evt_venue=''; $this->evt_start_date=''; $this->evt_end_date=''; $this->evt_type='seminar'; $this->evt_status='draft'; $this->evt_max_attendees=null; $this->evt_ticket_price=0; }

    private function loadCourse($id) { $c = Course::findOrFail($id); $this->crs_id=$c->id; $this->crs_title=$c->title; $this->crs_slug=$c->slug; $this->crs_description=$c->description; $this->crs_category=$c->category; $this->crs_level=$c->level; $this->crs_status=$c->status; $this->crs_duration_hours=$c->duration_hours; }
    private function resetCourseFields() { $this->crs_id=null; $this->crs_title=''; $this->crs_slug=''; $this->crs_description=''; $this->crs_category=''; $this->crs_level='beginner'; $this->crs_status='draft'; $this->crs_duration_hours=0; }

    public function savePage()
    {
        $this->validate(['page_title' => 'required|string|max:255']);
        if (!$this->page_slug) $this->page_slug = Str::slug($this->page_title);

        CmsPage::updateOrCreate(['id' => $this->page_id], [
            'title' => $this->page_title, 'slug' => $this->page_slug, 'content' => $this->page_content,
            'meta_title' => $this->page_meta_title, 'meta_description' => $this->page_meta_description,
            'status' => $this->page_status, 'sort_order' => $this->page_sort_order, 'created_by' => Auth::id(),
            'published_at' => $this->page_status === 'published' ? now() : null,
        ]);
        session()->flash('success', 'Page saved!'); $this->closeModal();
    }

    public function saveBlog()
    {
        $this->validate(['blog_title' => 'required|string|max:255']);
        if (!$this->blog_slug) $this->blog_slug = Str::slug($this->blog_title);

        BlogPost::updateOrCreate(['id' => $this->blog_id], [
            'title' => $this->blog_title, 'slug' => $this->blog_slug, 'content' => $this->blog_content,
            'excerpt' => $this->blog_excerpt, 'category' => $this->blog_category, 'tags' => $this->blog_tags,
            'status' => $this->blog_status, 'author_id' => Auth::id(),
            'published_at' => $this->blog_status === 'published' ? now() : null,
        ]);
        session()->flash('success', 'Blog post saved!'); $this->closeModal();
    }

    public function saveAppointment()
    {
        $this->validate(['apt_title' => 'required|string|max:255', 'apt_start_time' => 'required|date', 'apt_end_time' => 'required|date|after:apt_start_time']);

        Appointment::updateOrCreate(['id' => $this->apt_id], [
            'title' => $this->apt_title, 'description' => $this->apt_description,
            'customer_id' => $this->apt_customer_id ?: null, 'assigned_to' => $this->apt_assigned_to ?: null,
            'start_time' => $this->apt_start_time, 'end_time' => $this->apt_end_time,
            'location' => $this->apt_location, 'status' => $this->apt_status,
            'type' => $this->apt_type, 'notes' => $this->apt_notes,
        ]);
        session()->flash('success', 'Appointment saved!'); $this->closeModal();
    }

    public function saveEvent()
    {
        $this->validate(['evt_name' => 'required|string|max:255', 'evt_start_date' => 'required|date']);

        Event::updateOrCreate(['id' => $this->evt_id], [
            'name' => $this->evt_name, 'description' => $this->evt_description, 'venue' => $this->evt_venue,
            'start_date' => $this->evt_start_date, 'end_date' => $this->evt_end_date,
            'type' => $this->evt_type, 'status' => $this->evt_status,
            'max_attendees' => $this->evt_max_attendees, 'ticket_price' => $this->evt_ticket_price,
            'organizer_id' => Auth::id(),
        ]);
        session()->flash('success', 'Event saved!'); $this->closeModal();
    }

    public function saveCourse()
    {
        $this->validate(['crs_title' => 'required|string|max:255']);
        if (!$this->crs_slug) $this->crs_slug = Str::slug($this->crs_title);

        Course::updateOrCreate(['id' => $this->crs_id], [
            'title' => $this->crs_title, 'slug' => $this->crs_slug, 'description' => $this->crs_description,
            'category' => $this->crs_category, 'level' => $this->crs_level, 'status' => $this->crs_status,
            'duration_hours' => $this->crs_duration_hours, 'instructor_id' => Auth::id(),
        ]);
        session()->flash('success', 'Course saved!'); $this->closeModal();
    }

    public function delete($type, $id)
    {
        match($type) {
            'page' => CmsPage::findOrFail($id)->delete(),
            'blog' => BlogPost::findOrFail($id)->delete(),
            'appointment' => Appointment::findOrFail($id)->delete(),
            'event' => Event::findOrFail($id)->delete(),
            'course' => Course::findOrFail($id)->delete(),
        };
        session()->flash('success', ucfirst($type) . ' deleted!');
    }

    public function render()
    {
        $s = '%' . $this->search . '%';
        return view('livewire.website-cms-manager', [
            'pages' => CmsPage::where('title', 'like', $s)->orderByDesc('id')->paginate(10, ['*'], 'pagesPage'),
            'blogs' => BlogPost::where('title', 'like', $s)->orderByDesc('id')->paginate(10, ['*'], 'blogsPage'),
            'appointments' => Appointment::with(['customer', 'assignee'])->where('title', 'like', $s)->orderByDesc('start_time')->paginate(10, ['*'], 'aptsPage'),
            'events' => Event::withCount('attendees')->where('name', 'like', $s)->orderByDesc('start_date')->paginate(10, ['*'], 'evtsPage'),
            'courses' => Course::withCount(['lessons', 'enrollments'])->where('title', 'like', $s)->orderByDesc('id')->paginate(10, ['*'], 'crsPage'),
            'customers' => Customer::orderBy('name')->get(),
            'users' => User::orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}
