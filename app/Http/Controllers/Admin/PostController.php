<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\PostDataTable;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    public function index(PostDataTable $dataTable)
    {
        return $dataTable->render('admin.post.index');
    }

    public function new(Request $request)
    {
        return view('admin.post.new');
    }

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required',
            'image' => 'required',
        ], [
            'title.required' => 'Tiêu đề bài viết không được để trống!',
            'content.required' => 'Nội dung bài viết không được để trống!',
            'image.required' => 'Hình ảnh hiển thị bài viết phải được tải lên!',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        //Xử lý Ảnh trong nội dung
        $content = $request->content;

//    $dom = new \DomDocument();
//
//    // conver utf-8 to html entities
//    $content = mb_convert_encoding($content, 'HTML-ENTITIES', "UTF-8");
//
//    $dom->loadHtml($content, LIBXML_HTML_NODEFDTD);
//
//    $images = $dom->getElementsByTagName('img');
//
//    foreach($images as $k => $img){
//
//        $data = $img->getAttribute('src');
//
//        if(Str::containsAll($data, ['data:image', 'base64'])){
//
//            list(, $type) = explode('data:image/', $data);
//            list($type, ) = explode(';base64,', $type);
//
//            list(, $data) = explode(';base64,', $data);
//
//            $data = base64_decode($data);
//
//            $image_name= time().$k.'.'.$type;
//
//            Storage::disk('public')->put('images/posts/'.$image_name, $data);
//
//            $img->removeAttribute('src');
//            $img->setAttribute('src', '/storage/images/posts/'.$image_name);
//        }
//    }
//
//    $content = $dom->saveHTML();
//
//    //conver html-entities to utf-8
//    $content = mb_convert_encoding($content, "UTF-8", 'HTML-ENTITIES');
//
//    //get content
//    list(, $content) = explode('<html><body>', $content);
//    list($content, ) = explode('</body></html>', $content);

        $post = new Post;
        $post->title = $request->title;
        $post->content = $content;
        $post->user_id = Auth::user()->id;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image_name = time() . '_' . $image->getClientOriginalName();
            $image->storeAs('images/posts', $image_name, 'public');
            $post->image = $image_name;
        }

        $post->save();

        return redirect()->route('admin.post.index')->with([
            'alert' => [
                'type' => 'success',
                'title' => 'Thành Công',
                'content' => 'Bài viết của bạn đã được tạo thành công.'
            ]
        ]);
    }

    public function delete(Request $request)
    {
        $post = Post::where('id', $request->post_id)->first();

        if (!$post) {
            $data['type'] = 'error';
            $data['title'] = 'Thất Bại';
            $data['content'] = 'Bạn không thể xóa bài viết không tồn tại!';
            return response()->json($data, Response::HTTP_BAD_REQUEST);
        }

//            Storage::disk('public')->delete('images/posts/' . $post->image);
//            if ($post->content != null) {
//                $dom = new \DomDocument();
//
//                // conver utf-8 to html entities
//                $content = mb_convert_encoding($post->content, 'HTML-ENTITIES', "UTF-8");
//
//                $dom->loadHtml($content, LIBXML_HTML_NODEFDTD | LIBXML_NOERROR);
//
//                $images = $dom->getElementsByTagName('img');
//
//                foreach ($images as $img) {
//                    $src = $img->getAttribute('src');
//                    $src = mb_convert_encoding($src, "UTF-8", 'HTML-ENTITIES');
//
//                    if (Str::startsWith($src, '/storage/images/posts/')) {
//                        list(, $src) = explode('/storage/', $src);
//
//                        Storage::disk('public')->delete($src);
//                    }
//                }
//            }

        $post->delete();

        $data['type'] = 'success';
        $data['title'] = 'Thành Công';
        $data['content'] = 'Xóa bài viết thành công!';

        return response()->json($data, Response::HTTP_OK);
    }

    public function edit($id)
    {
        $post = Post::where('id', $id)->first();
        if (!$post) {
            abort(404);
        }
        return view('admin.post.edit')->with('post', $post);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required',
        ], [
            'title.required' => 'Tiêu đề bài viết không được để trống!',
            'content.required' => 'Nội dung bài viết không được để trống!',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        //Xử lý Ảnh trong nội dung
        $content = $request->content;

//    $dom = new \DomDocument();
//
//    // conver utf-8 to html entities
//    $content = mb_convert_encoding($content, 'HTML-ENTITIES', "UTF-8");
//
//    $dom->loadHtml($content, LIBXML_HTML_NODEFDTD);
//
//    $images = $dom->getElementsByTagName('img');
//
//    foreach($images as $k => $img){
//
//        $data = $img->getAttribute('src');
//
//        if(Str::containsAll($data, ['data:image', 'base64'])){
//
//            list(, $type) = explode('data:image/', $data);
//            list($type, ) = explode(';base64,', $type);
//
//            list(, $data) = explode(';base64,', $data);
//
//            $data = base64_decode($data);
//
//            $image_name= time().$k.'.'.$type;
//
//            Storage::disk('public')->put('images/posts/'.$image_name, $data);
//
//            $img->removeAttribute('src');
//            $img->setAttribute('src', '/storage/images/posts/'.$image_name);
//        }
//    }
//
//    $content = $dom->saveHTML();
//
//    //conver html-entities to utf-8
//    $content = mb_convert_encoding($content, "UTF-8", 'HTML-ENTITIES');
//
//    //get content
//    list(, $content) = explode('<html><body>', $content);
//    list($content, ) = explode('</body></html>', $content);

        $post = Post::where('id', $id)->first();
        $post->title = $request->title;
        $post->content = $content;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image_name = time() . '_' . $image->getClientOriginalName();
            $image->storeAs('images/posts', $image_name, 'public');
            Storage::disk('public')->delete('images/posts/' . $post->image);
            $post->image = $image_name;
        }

        $post->save();

        return redirect()->route('admin.post.index')->with([
            'alert' => [
                'type' => 'success',
                'title' => 'Thành Công',
                'content' => 'Chỉnh sửa bài viết thành công.'
            ]
        ]);
    }
}
