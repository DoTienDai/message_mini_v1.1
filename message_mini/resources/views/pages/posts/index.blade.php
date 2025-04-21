@extends('layouts.app')
@section('title', 'Bài viết')
@section('description', 'Bài viết')
@section('keyword', 'Bài viết')
@push('styles')
    <style>
        .post form {
            margin: 20px 25px;
        }

        .post form .content img {
            cursor: default;
            max-width: 52px;
        }

        .post form .content .details {
            margin: -3px 0 0 12px;
        }

        form .content .details p {
            font-size: 17px;
            font-weight: 500;
        }

        form :where(textarea, button) {
            width: 100%;
            outline: none;
            border: none;
        }

        form textarea {
            resize: none;
            font-size: 18px;
            margin-top: 30px;
            min-height: 100px;
        }

        form textarea::placeholder {
            color: #858585;
        }

        form textarea:focus::placeholder {
            color: #b3b3b3;
        }

        .theme-emoji img:last-child {
            max-width: 24px;
        }

        form .options {
            height: 57px;
            margin: 15px 0;
            padding: 0 15px;
            border-radius: 7px;
            border: 1px solid #B9B9B9;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        form .options :where(.list, li) {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        form .options p {
            color: #595959;
            font-size: 15px;
            font-weight: 500;
            cursor: default;
        }

        .options .list li {
            height: 42px;
            width: 42px;
            margin: 0 -1px;
            cursor: pointer;
            border-radius: 50%;
        }

        form button {
            height: 53px;
            color: #fff;
            font-size: 18px;
            font-weight: 500;
            cursor: pointer;
            color: #BCC0C4;
            cursor: no-drop;
            border-radius: 7px;
            background: #e2e5e9;
            transition: all 0.3s ease;
        }

        form textarea:valid~button,
        form button.active {
            color: #fff;
            cursor: pointer;
            background: #4599FF;
        }

        form textarea:valid~button:hover,
        form button.active:hover {
            background: #1a81ff;
        }

        /*  */

        #post_card.card {
            width: 600px;
            box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.15),
                -5px -5px 5px rgba(0, 0, 0, 0.15);
            padding: 20px;
        }

        #post_card.card .top {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        #post_card.card .top .user_details {
            display: flex;
            align-items: center;
        }

        #post_card.card .top .user_details .profile_img {
            position: relative;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 8px;
        }

        #post_card .cover {
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            cursor: pointer;
        }

        #post_card.card .top .user_details h3 {
            font-size: 18px;
            color: black;
            font-weight: 900;
        }


        #post_card .globDot {
            position: absolute;
            margin-left: 5px;
            margin-top: -4px;
            font-size: 20px;
            align-items: center;
            color: #0000004b;
        }

        #post_card .coverFull {
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            cursor: pointer;
        }

        #post_card .right h4 {
            margin-top: 5px;
            margin-left: 33px;
            font-size: 17px;
            color: #777;
            text-align: center;
            font-weight: 500;
        }

        #post_card .addComments {
            display: flex;
            align-items: center;
            margin-top: 20px;
        }

        #post_card .addComments .userimg {
            position: relative;
            min-width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 8px;
        }

        #post_card .text {
            background: #F0F2F5;
            width: 100%;
            height: 40px;
            border: none;
            outline: none;
            font-weight: 400;
            font-size: 16px;
            color: #262626;
            border-radius: 20px;
        }

        #post_card input[type="text"] {
            position: relative;
            padding: 0 25px;
        }

        /*  */

        .header-blogs {
            position: fixed;
            width: 100%;
            z-index: 1;
        }

        .card_post {
            margin-top: 70px;
        }

        .post-images img {
            width: 150px;
            /* Đặt kích thước cố định cho ô vuông */
            height: 150px;
            /* Đặt kích thước cố định cho ô vuông */
            object-fit: scale-down;
            /* Cắt hình ảnh để phù hợp với kích thước ô vuông */
            margin: 5px;
            /* Khoảng cách giữa các hình ảnh */
        }

        /*  */

        .replies {
             margin-left: 20px; /* Thụt lề các phản hồi */
        }

        .replies li {
            list-style-type: none; /* Loại bỏ dấu đầu dòng */
        }

        .comments-list {
            max-height: 300px;
            overflow-y: auto;
        }
    </style>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css">
@endpush

@section('content-1')
    <div class="container">
        <section class="post">
            <form action="{{ route('posts.store') }}" enctype="multipart/form-data" method="POST">
                @csrf
                <div class="content d-flex">
                    <img class="rounded-circle" src="{{ asset(auth()->user()->avatar) }}" alt="logo">
                    <div class="ms-2">
                        <p class="mb-0 fw-semibold">{{ auth()->user()->name }}</p>

                        <select class="form-select form-select-sm" aria-label=".form-select-sm example" name="privacy">
                            <option value="public" selected>Công khai</option>
                            <option value="friend">Bạn bè</option>
                            <option value="private">Riêng tư</option>
                        </select>
                    </div>

                </div>
                @if ($errors->has('content'))
                    <div class="alert alert-danger">
                        {{ $errors->first('content') }}
                    </div>
                @endif

                <textarea placeholder="Bạn đang nghĩ gì?" spellcheck="false" required name="content" id="content">{{ old('content') }}</textarea>
                <div id="imagePreview" class="d-flex flex-wrap"></div>
                <div class="theme-emoji d-flex align-items-center justify-content-between">
                    <img src="{{ asset('assets/images/svg/theme.svg') }}" alt="theme">
                    <img src="{{ asset('assets/images/svg/smile.svg') }}" alt="smile">
                </div>
                <div class="options d-flex align-items-center justify-content-between">
                    <p class="mb-0">Thêm vào bài viết</p>
                    <ul class="list mb-0">
                        <li><img src="{{ asset('assets/images/svg/gallery.svg') }}" alt="gallery" id="gallery"></li>
                        <li><img src="{{ asset('assets/images/svg/tag.svg') }}" alt="gallery"></li>
                        {{-- <li><img src="{{ asset('assets/images/svg/emoji.svg') }}" alt="gallery"></li> --}}
                        {{-- <li><img src="{{ asset('assets/images/svg/mic.svg') }}" alt="gallery"></li> --}}
                        <li><img src="{{ asset('assets/images/svg/more.svg') }}" alt="gallery"></li>
                    </ul>
                </div>
                <input type="file" id="imageInput" name="images[]" accept="image/*" multiple style="display: none;">

                <button type="submit" id="submitButton" disabled>Post</button>
            </form>
        </section>
    </div>
@endsection

@section('content-2')
    <div class="header-blogs bg-white px-2 py-3 border-bottom">
        <a href="{{ route('posts.index') }}" class="d-flex align-items-center text-decoration-none text-dark">
            <i class="fa-solid fa-globe fa-xl me-2"></i>
            <h3 class="mb-0">Tất cả bài viết</h3>
        </a>
    </div>

    <section class="d-flex flex-column align-items-center card_post">
        @foreach ($posts as $item)
            <div class="card mt-2" id="post_card">
                <div class="top">
                    <div class="user_details">
                        <div class="profile_img">
                            <img src="{{ $item->user->avatar ? asset($item->user->avatar) : asset('/assets/images/avatar_default.jpg') }}"
                                alt="user" class="cover">
                        </div>
                        <h3>{{ $item->user->name }}<br>
                            <div>
                                <span class="text-secondary me-2">{{ $item->formatted_time }}</span>

                                @if ($item->privacy == 'public')
                                    <i class="fa-solid fa-globe"></i>
                                @elseif ($item->privacy == 'friend')
                                    <i class="fas fa-user-friends"></i>
                                @elseif ($item->privacy == 'private')
                                    <i class="fas fa-lock"></i>
                                @endif

                            </div>
                        </h3>
                    </div>
                    <div class="dot">
                        
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-ellipsis fa-lg"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <form class="privacy-form" data-post-id="{{ $item->id }}">
                                        <select class="form-select privacy-select" aria-label="Chọn quyền riêng tư">
                                            <option value="public" {{ $item->privacy == 'public' ? 'selected' : '' }}>Công khai</option>
                                            <option value="friend" {{ $item->privacy == 'friends' ? 'selected' : '' }}>Bạn bè</option>
                                            <option value="private" {{ $item->privacy == 'private' ? 'selected' : '' }}>Riêng tư</option>
                                        </select>
                                    </form>
                                </li>
                                @if($item->user_id == auth()->id())
                                <li>
                                    <a class="dropdown-item text-danger" href="#" onclick="showDeleteModal({{ $item->id }})">
                                        <i class="fa-solid fa-trash text-danger"></i> Xóa bài viết
                                    </a>
                                </li>
                                @endif
                            </ul>
                          </div>
                    </div>
                </div>
                <div class="modal fade" id="deletePostModal" tabindex="-1" aria-labelledby="deletePostModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deletePostModalLabel">Xác nhận xóa bài viết</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Bạn có chắc chắn muốn xóa bài viết này không?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                <button type="button" class="btn btn-danger" id="confirmDeletePost">Xóa</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content mt-2">
                    <p class="message">{!! $item->content !!}</p>

                    @if (is_array($item->images) && !empty($item->images))
                        <div class="post-images row">
                            @foreach ($item->images as $image)
                                <a class="text-decoration-none col-4" href="{{ asset($image) }}" data-fancybox="gallery">
                                    <img src="{{ asset($image) }}" alt="Post Image" class="img-thumbnail img-fluid">
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="d-flex align-items-center justify-content-between border-bottom border-3">
                    <a class="left d-flex align-items-center text-decoration-none" data-bs-toggle="modal" data-bs-target="#likesModal{{ $item->id }}" data-post-id="{{ $item->id }}">
                        <p class="likes ms-2 mb-0 fw-bold text-dark" data-post-id="{{ $item->id }}">{{ $item->likes->count() }} Likes</p>
                    </a>

                     <!-- Modal để hiển thị danh sách người đã like -->
                    <div class="modal fade" id="likesModal{{ $item->id }}" tabindex="-1" aria-labelledby="likesModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="likesModalLabel">Danh sách người đã like</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <ul id="likesList{{ $item->id }}" class="list-group">
                                        <!-- Danh sách người đã like sẽ được tải vào đây -->
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="right">
                        <p>{{ $item->comment_count }} Comments</p>
                    </div>
                </div>

                <div class="icon mt-3 p-2">
                    <div class="d-flex align-items-center justify-content-between">
                        <a class="like-button text-dark" data-post-id="{{ $item->id }}">
                            @if ($item->is_liked)
                                <i class="fa-solid fa-thumbs-up fa-2xl text-primary"></i>
                            @else
                                <i class="fa-regular fa-thumbs-up fa-2xl"></i>
                            @endif
                        </a>

                       <!-- Hiển thị biểu tượng comment -->
                        <a class="comment-button" data-post-id="{{ $item->id }}">
                            <i class="fa-regular fa-comment fa-2xl"></i>
                        </a>
                        {{-- <i class="fa-solid fa-share fa-2xl"></i> --}}
                    </div>
                </div>

                <!-- Hiển thị danh sách comment -->
                <ul id="commentsList{{ $item->id }}" class="border-top border-3 pt-3 comments-list list-unstyled d-flex flex-column-reverse">
                    @foreach($item->comments->take(3) as $comment)
                        <li>
                            @if (!$comment->parent_id)
                                <span class="fw-bold">{{ $comment->user->name }}:</span> {{ $comment->content }}
                                <p>{{ $comment->formatted_time }}</p>
                                <!-- Nút Reply -->
                                <a href="#" class="reply-button" data-comment-id="{{ $comment->id }}">Reply</a>
                            @endif
                           
                            <!-- Hiển thị reply -->
                            <ul class="replies">
                                @foreach($comment->replies as $reply)
                                    <li>
                                       <span class="fw-bold">{{ $reply->user->name }}: </span> {{ $reply->content }}
                                        <p>{{ $reply->formatted_time }}</p>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                </ul>

                <div class="addComments ">
                    <div class="userimg">
                        <img src="{{ auth()->user()->avatar ? asset(auth()->user()->avatar) : asset('/assets/images/avatar_default.jpg') }}"
                            alt="user" class="cover">
                    </div>
                    <textarea class="text" placeholder="Viết bình luận ..." maxlength="2000" rows="1" data-post-id="{{ $item->id }}"></textarea>
                </div>
            </div>
        @endforeach

    </section>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>
    
    {{-- chọn ảnh để up --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const gallery = document.getElementById('gallery');
            const imageInput = document.getElementById('imageInput');
            const imagePreview = document.getElementById('imagePreview');
            const content = document.getElementById('content');
            const submitButton = document.getElementById('submitButton');

            gallery.addEventListener('click', function() {
                imageInput.click();
            });

            imageInput.addEventListener('change', function() {
                imagePreview.innerHTML = ''; // Clear previous images
                const files = imageInput.files;

                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.classList.add('img-thumbnail', 'm-2');
                        img.style.maxWidth = '100px';
                        img.style.maxHeight = '100px';
                        img.style.objectFit = 'scale-down';
                        imagePreview.appendChild(img);
                    };

                    reader.readAsDataURL(file);
                }

                updateSubmitButtonState();
            });

            content.addEventListener('input', function() {
                updateSubmitButtonState();
            });

            function updateSubmitButtonState() {
                if (content.value.trim() !== '' || imageInput.files.length > 0) {
                    submitButton.disabled = false;
                    submitButton.classList.add('active');
                } else {
                    submitButton.disabled = true;
                    submitButton.classList.remove('active');
                }
            }
        });
    </script>

    {{-- like/unlike bài viết --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.like-button').forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    const postId = this.getAttribute('data-post-id');

                    fetch(`/posts/${postId}/like`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Cập nhật số lượng like
                            const likeCountElement = document.querySelector(`.likes[data-post-id="${postId}"]`);
                            if (likeCountElement) {
                                likeCountElement.textContent = `${data.likes_count} Likes`;
                            }

                            // Cập nhật trạng thái like/unlike
                            const icon = this.querySelector('i');
                            if (data.status === 'liked') {
                                icon.classList.remove('fa-regular');
                                icon.classList.add('fa-solid', 'text-primary');
                            } else {
                                icon.classList.remove('fa-solid', 'text-primary');
                                icon.classList.add('fa-regular');
                            }
                        }
                    })
                    .catch(error => console.error('Error:', error));
                });
            });
        });
    </script>

    {{-- Hiển thị danh sách người đã like --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const likesModal = document.querySelectorAll('[data-bs-toggle="modal"]');
            likesModal.forEach(button => {
                button.addEventListener('click', function(event) {
                    const postId = this.getAttribute('data-post-id');
                    const likesList = document.getElementById(`likesList${postId}`);

                    // Xóa danh sách cũ
                    likesList.innerHTML = '';

                    // Gửi yêu cầu AJAX để lấy danh sách người đã like
                    fetch(`/posts/${postId}/likes`)
                        .then(response => response.json())
                        .then(data => {
                            data.likes.forEach(like => {
                                const listItem = document.createElement('li');
                                listItem.classList.add('list-group-item');
                                listItem.innerHTML = `
                                    <div class="d-flex align-items-center">
                                        <img src="${like.user.avatar ? like.user.avatar : '/assets/images/avatar_default.jpg'}" alt="User" class="rounded-circle me-3 img-avatar" style="object-fit: cover" width="50" height="50">
                                        <div class="chat-info">
                                            <h5 class="mb-0 text-dark fw-semibold">${like.user.name}</h5>
                                            <p class="text-muted mb-0 text-message">${like.formatted_time}</p>
                                        </div>
                                    </div>
                                `;
                                likesList.appendChild(listItem);
                            });
                        })
                        .catch(error => console.error('Error:', error));
                });
            });
        });
    </script>
    
        {{-- Hiển thị danh sách bình luận --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.comment-button').forEach(button => {
                    button.addEventListener('click', function(event) {
                        event.preventDefault();
                        const postId = this.getAttribute('data-post-id');
                        const commentsList = document.getElementById(`commentsList${postId}`);
    
                        // Gửi yêu cầu AJAX để lấy thêm bình luận
                        fetch(`/posts/${postId}/comments`)
                            .then(response => response.json())
                            .then(data => {
                                commentsList.innerHTML = ''; // Xóa danh sách cũ
                                data.comments.forEach(comment => {
                                    const listItem = document.createElement('li');
                                    listItem.innerHTML = `
                                        ${comment.user.name}: ${comment.content}
                                        <p>${comment.formatted_time}</p>
                                        <ul>
                                            ${comment.replies.map(reply => `
                                                <li>${reply.user.name}: ${reply.content}</li>
                                                <p>${reply.formatted_time}</p>
                                            `).join('')}
                                        </ul>
                                    `;
                                    commentsList.appendChild(listItem);
                                });
                            })
                            .catch(error => console.error('Error:', error));
                    });
                });
            });
        </script>

    {{-- Thêm bình luận --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Giới hạn số dòng của textarea
            document.addEventListener('input', function(event) {
                if (event.target.matches('.text')) {
                    const textarea = event.target;
                    const maxRows = 3;
                    const lineHeight = parseInt(window.getComputedStyle(textarea).lineHeight);
                    const rows = Math.floor(textarea.scrollHeight / lineHeight);
                    if (rows > maxRows) {
                        textarea.style.overflowY = 'auto';
                    } else {
                        textarea.style.overflowY = 'hidden';
                        textarea.rows = rows;
                    }
                }
            });

            // Xử lý sự kiện gửi bình luận
            document.addEventListener('keypress', function(event) {
                if (event.target.matches('.text') && event.key === 'Enter' && !event.shiftKey) {
                    event.preventDefault();
                    const textarea = event.target;
                    const postId = textarea.getAttribute('data-post-id');
                    const content = textarea.value.trim();

                    if (content.length > 0) {
                        fetch(`/posts/${postId}/comments`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ content })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const commentsList = document.getElementById(`commentsList${postId}`);
                                const listItem = document.createElement('li');
                                listItem.innerHTML = `
                                    <span class="fw-bold">${data.comment.user.name}: </span> ${data.comment.content}
                                    <p>${data.comment.formatted_time}</p>
                                    <a href="#" class="reply-button" data-comment-id="${data.comment.id}">Reply</a>
                                    <ul class="replies"></ul>
                                `;
                                commentsList.appendChild(listItem);
                                textarea.value = '';
                                textarea.rows = 1;
                            }
                        })
                        .catch(error => console.error('Error:', error));
                    }
                }
            });

            // Xử lý sự kiện nhấp vào nút Reply
            document.addEventListener('click', function(event) {
                if (event.target.matches('.reply-button')) {
                    event.preventDefault();
                    const button = event.target;
                    const commentId = button.getAttribute('data-comment-id');
                    const replyList = button.nextElementSibling;

                    // Thêm textarea để nhập phản hồi
                    const replyTextarea = document.createElement('textarea');
                    replyTextarea.classList.add('reply-text');
                    replyTextarea.classList.add('text');
                    replyTextarea.setAttribute('placeholder', 'Viết phản hồi ...');
                    replyTextarea.setAttribute('maxlength', '2000');
                    replyTextarea.setAttribute('rows', '1');
                    replyTextarea.setAttribute('data-comment-id', commentId);

                    // Thêm sự kiện gửi phản hồi
                    replyTextarea.addEventListener('keypress', function(event) {
                        if (event.key === 'Enter' && !event.shiftKey) {
                            event.preventDefault();
                            const content = replyTextarea.value.trim();

                            if (content.length > 0) {
                                fetch(`/comments/${commentId}/replies`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({ content })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        const replyItem = document.createElement('li');
                                        replyItem.innerHTML = `
                                            <span class="fw-bold">${data.reply.user.name}: </span> ${data.reply.content}
                                            <p>${data.reply.formatted_time}</p>
                                        `;
                                        replyList.appendChild(replyItem);
                                        replyTextarea.remove();
                                    }
                                })
                                .catch(error => console.error('Error:', error));
                            }
                        }
                    });

                    replyList.appendChild(replyTextarea);
                }
            });
        });
    </script>
<script>
    let postIdToDelete = null;

    function showDeleteModal(postId) {
        postIdToDelete = postId;
        const deleteModal = new bootstrap.Modal(document.getElementById('deletePostModal'));
        deleteModal.show();
    }

    document.getElementById('confirmDeletePost').addEventListener('click', function() {
        if (postIdToDelete) {
            deletePost(postIdToDelete);
        }
    });

    function deletePost(postId) {
        fetch(`/posts/${postId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
</script>
@endpush
