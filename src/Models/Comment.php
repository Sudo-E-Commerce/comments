<?php

namespace Sudo\Comment\Models;

use Sudo\Base\Models\BaseModel;

class Comment extends BaseModel {
	
	public $timestamps = false;
	
	/**
	 * Thêm bình luận
	 * @param array 		$data: chứa các thông số truyền vào của 1 comments (name, phone, email, content)
	 */
	public static function add($data, $status = 1) {
		$data = json_decode(json_encode(removeScriptArray($data)));
		$time = date('Y-m-d H:i:s');
		// Kiểm tra trùng bình luận
		$check_exist = Comment::where('parent_id', $data->parent_id)
								->where('name', $data->name)
								->where('phone', $data->phone)
								->where('email', $data->email)
								->where('content', $data->content)
								->where('type', $data->type)
								->where('type_id', $data->type_id)
								->where('status', $status)
								->first();
		if ($check_exist == null) {
			// Bản ghi của bình luận cha
			$parent_record = Comment::where('id', $data->parent_id)->first();
			// Nếu bình luận cha có parent_id thì sẽ parent_id cha của bình luận cha
			if (isset($parent_record->parent_id) && !empty($parent_record->parent_id)) {
				$parent_id = $parent_record->parent_id;
			} else {
				$parent_id = $data->parent_id ?? 0;
			}
			// Thêm bình luận
			Comment::insert([
				'admin_id' 			=> $data->admin_id,
				'parent_id' 		=> $parent_id ?? 0,
                'type' 				=> $data->type ?? '',
                'type_id' 			=> $data->type_id ?? 0,
                'name' 				=> $data->name ?? '',
                'email' 			=> $data->email ?? '',
                'phone' 			=> $data->phone ?? '',
                'ip' 				=> getClientIp(),
                'content' 			=> $data->content ?? '',
                'image' 			=> $data->image ?? null,
                'status' 			=> $status,
                'time' 				=> $time
			]);
		}
	}

	/**
	 * Lấy toàn bộ bình luận của module theo id của module đó
	 * @param string 		$type: Tên module
	 * @param number 		$type_id: id của module
	 */
	public static function loadComment($type, $type_id) {
		// Tổng comments cả cha và con
		$comment_totals = Comment::where('type', $type)
									->where('type_id', $type_id)
									->where('status', 1)
									->count();
		// Comment cha
		$comment_parents = Comment::where('parent_id', 0)
									->where('type', $type)
									->where('type_id', $type_id)
									->where('status', 1)
									->orderBy('id','desc')
									->paginate(config('SudoComment.page_number'), ['*'], 'pageCmt');
		// Bình luận con
		$comment_childs = Comment::where('status', 1)
									->whereIn('parent_id',$comment_parents->pluck('id')->toArray())
									->orderBy('id','asc')
									->get();
		return [
			'comment_totals' 	=> $comment_totals,
			'comment_parents' 	=> $comment_parents,
			'comment_childs' 	=> $comment_childs,
		];

	}

	public static function searchComment($type, $type_id, $keyword) {
		$keyword = formatKeyword($keyword);
		// Comment cha
		$comment_parents = Comment::where('parent_id', 0)
									->where('name', 'LIKE', '%'.$keyword['key'].'%')
									->orWhere('content', 'LIKE', '%'.$keyword['key'].'%')
									->where('type', $type)
									->where('type_id', $type_id)
									->where('status', 1)
									->orderBy('id','desc')
									->paginate(config('SudoComment.page_number'), ['*'], 'pageCmt');
		// Bình luận con
		$comment_childs = Comment::where('status', 1)
									->where('name', 'LIKE', '%'.$keyword['key'].'%')
									->orWhere('content', 'LIKE', '%'.$keyword['key'].'%')
									->whereIn('parent_id',$comment_parents->pluck('id')->toArray())
									->orderBy('id','asc')
									->get();

		return [
			'comment_parents' 	=> $comment_parents,
			'comment_childs' 	=> $comment_childs,
		];
	}

	public function getName() {
		return $this->name;
	}

	public function getTime($field = 'time', $format = 'H:i d/m/Y') {
		return changeTimeToText($this->$field, $format);
	}
}