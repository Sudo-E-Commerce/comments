<?php

namespace Sudo\Comment\Http\Controllers;

use Illuminate\Http\Request;

class AjaxController
{
	public function loadComment(Request $request) {
		$type 				= $request->type;
		$type_id 			= $request->type_id;

		$comments 			= \Sudo\Comment\Models\Comment::loadComment($type, $type_id);
		$comment_totals 	= $comments['comment_totals'] ?? 0;
		$comment_parents	= $comments['comment_parents'] ?? [];
		$comment_childs 	= $comments['comment_childs'] ?? [];

		$view_name 			= 'Comment::item';
		$html = view($view_name)->with([
			'comment_parents' 	=> $comment_parents,
			'comment_childs' 	=> $comment_childs,
		])->render();

		return [
			'comment_totals' => $comment_totals,
			'has_more' => ($comment_parents->lastPage() == $comment_parents->currentPage()) ? false : true,
			'html' => $html,
		];
	}

	public function addComment(Request $requests) {
		$status = 2;
		$message = __('Bình luận không thành công!');
		try {
            $type 		= $requests->type;
            $type_id 	= $requests->type_id;
			$content 	= $requests->content;
            $name 		= $requests->name;
            $phone 		= $requests->phone;
            $email 		= $requests->email;
            $parent_id 	= $requests->parent_id??0;
			$files 		= $requests->file('files')??null;
			if (isset($content) && !empty($content)) {
				try {
					$image = null;
					if (config('SudoComment.upload_image') == true) {
						if (isset($files) && count($files) > 0) {
							// Mảng ảnh trống
                        	$array_image = [];
                        	// Lặp và tải ảnh lên
                        	foreach ($files as $file) {
                        		$file_info = uploadFile($file);
                        		if (isset($file_info['url']) && !empty($file_info['url'])) {
                        			$array_image[] = $file_info['url'];
                        		}
                        	}
                        	$image = implode(',', $array_image);
						};
					}
					$data = [
			            'admin_id' 		=> 0,
			            'parent_id' 	=> $parent_id,
			            'type' 			=> $type,
			            'type_id' 		=> $type_id,
			            'name' 			=> $name,
			            'phone' 		=> $phone,
			            'email' 		=> $email,
			            'content' 		=> $content,
			            'image' 		=> $image,
			        ];
					\Sudo\Comment\Models\Comment::add($data, config('SudoComment.status_default'));
					$status = 1;
					$message = __('Bình luận thành công!');
				} catch (\Exception $e) {
					\Log::error($e);
				}
			}
		} catch (\Exception $e) {
			\Log::error($e);
		}
		return [
			'status' => $status,
			'message' => $message,
		];
	}

	public function searchComment(Request $request) {
		$type 				= $request->type;
		$type_id 			= $request->type_id;
		$keyword 			= $request->keyword;
		$comments 			= \Sudo\Comment\Models\Comment::searchComment($type, $type_id, $keyword);
		$comment_parents	= $comments['comment_parents'] ?? [];
		$comment_childs 	= $comments['comment_childs'] ?? [];

		$view_name 			= 'Comment::item';
		$html = view($view_name)->with([
			'comment_parents' 	=> $comment_parents,
			'comment_childs' 	=> $comment_childs,
		])->render();
		return [
			'has_more' => ($comment_parents->lastPage() == $comment_parents->currentPage()) ? false : true,
			'html' => $html,
		];
	}
}
