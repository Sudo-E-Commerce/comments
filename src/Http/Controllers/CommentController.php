<?php

namespace Sudo\Comment\Http\Controllers;
use Sudo\Base\Http\Controllers\AdminController;

use Illuminate\Http\Request;
use ListData;
use Form;

class CommentController extends AdminController
{
	function __construct() {
        $this->models = new \Sudo\Comment\Models\Comment;
        $this->table_name = $this->models->getTable();
        $this->module_name = 'Bình luận';
        $this->has_seo = false;
        $this->has_locale = false;
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $requests) {

        \Asset::addDirectly(asset('platforms/comments/admin/js/comments.js'), 'scripts', 'bottom');

    	$listdata = new ListData($requests, $this->models, 'Comment::table.index', $this->has_locale);
        // Build Form tìm kiếm
        $listdata->search('name', 'Tên người bình luận', 'string');
        $listdata->search('content', 'Nội dung bình luận', 'string');
        $listdata->search('time', 'Ngày bình luận', 'range');
        $listdata->search('status', 'Trạng thái', 'array', config('SudoComment.status'));
        $listdata->search('type', '', 'hidden');
        $listdata->search('type_id', '', 'hidden');
        $listdata->searchBtn('Xuất Excel', '#', 'primary', 'fas fa-file-excel');
        // Build các button hành động
        $listdata->btnAction('status', 1, __('Hiện'), 'success', 'fas fa-edit');
        $listdata->btnAction('status', 0, __('Ẩn'), 'info', 'fas fa-window-close');
        $listdata->btnAction('delete', -1, __('Table::table.trash'), 'danger', 'fas fa-trash');
        // Build bảng
        $listdata->add('', 'Thông tin chung', 0);
        $listdata->add('', 'Thông tin bình luận', 0);
        $listdata->add('', 'Bài bình luận', 0);
        $listdata->add('status', 'Trạng thái', 1, 'status', config('SudoComment.status'));
        $listdata->add('', 'Sửa', 0, 'edit');
        $listdata->add('', 'Xóa', 0, 'delete');
        // Không có thêm
        $listdata->no_add();

        return $listdata->render();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return redirect()->back();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $requests) {
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
    	return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        // Dẽ liệu bản ghi hiện tại
        $data_edit = $this->models->where('id', $id)->first();
        // Khởi tạo form
        $form = new Form;

        $form->disable($data_edit->name, 'Họ và tên');
        $form->disable($data_edit->phone, 'Điện thoại');
        $form->disable($data_edit->email, 'Email');
        $form->multiImage('image', explode(',', $data_edit->image), 0, 'Ảnh bình luận', 'ảnh');
        $form->textarea('content', $data_edit->content, 1, 'Nội dung bình luận');
        $form->radio('status', $data_edit->status, 'Trạng thái', config('SudoComment.status'));

        $form->action('edit');
        // Hiển thị form tại view
        return $form->render('edit', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $requests, $id) {
        // Xử lý validate
        validateForm($requests, 'content', 'Nội dung bình luận không được để trống.');
        // Lấy bản ghi
        $data_edit = $this->models->where('id', $id)->first();
        // Các giá trị mặc định
        $status = 0;
        // Đưa mảng về các biến có tên là các key của mảng
        extract($requests->all(), EXTR_OVERWRITE);
        // Chuẩn hóa lại dữ liệu
        if (isset($image) && !empty($image)) {
            $image = implode(',', $image);
        }
        // Các giá trị thay đổi
        $compact = compact('image','content','status');
        // Cập nhật tại database
        $this->models->updateRecord($requests, $id, $compact, $this->has_seo);
        // Điều hướng
        return redirect(route('admin.'.$this->table_name.'.'.$redirect, $id))->with([
            'type' => 'success',
            'message' => __('Core::admin.update_success')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
    	//
    }

    public function quickReply(Request $requests) {
        // Đưa mảng về các biến có tên là các key của mảng
        $parent_id = $requests->id;
        $status = 2;
        extract($requests->all(), EXTR_OVERWRITE);
        $data = [
            'admin_id'      => \Auth::guard('admin')->user()->id ?? 0,
            'parent_id'     => $parent_id,
            'type'          => $type,
            'type_id'       => $type_id,
            'name'          => $name,
            'phone'         => $phone,
            'email'         => $email,
            'content'       => $content,
        ];
        if (isset($image) && !empty($image)) {
            $data['image'] = implode(',', $image);
        }
        try {
            $this->models->add($data, 1);
            $status = 1;
            $message = 'Trả lời thành công.';
        } catch (\Exception $e) {
            $status = 2;
            $message = 'Core::admin.ajax_error_edit';
        }
        return [
            'status' => $status,
            'message' => __($message),
        ];
    }

    public function quickEdit(Request $requests) {
        // Kiếm tra xem có quyền hay không
        if (checkRole($this->table_name.'_edit')) {
            // Đưa mảng về các biến có tên là các key của mảng
            $id = $requests->id;
            $status = 2;
            extract($requests->all(), EXTR_OVERWRITE);
            // Kiểm tra có tồn tại bản ghi hay không
            $check = $this->models->where('id', $id)->first();
            if (!empty($check)) {
                $data = [
                    'content' => $content,
                ];
                if (isset($image) && !empty($image)) {
                    $data['image'] = implode(',', $image);
                } else {
                    $data['image'] = null;
                }
                // Ghi logs
                systemLogs('quick_update', $data, $this->table_name, $id);
                // Thay đổi dữ liệu
                $save_count = \DB::table($this->table_name)->where('id',$id)->update($data);
                // Kiểm tra dữ liệu đã được thay đổi hay chưa
                if ($save_count != 0) {
                    $status = 1;
                    $message = 'Core::admin.update_success';
                } else {
                    $message = 'Core::admin.ajax_error_edit';
                }
            } else {
                $message = 'Core::admin.no_data_edit';
            }
        } else {
            $message = 'Core::admin.no_permission';
        }
        return [
            'status' => $status,
            'message' => __($message),
        ];
    }
}