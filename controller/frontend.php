<?php

// Chargement des classes
require_once('model/PostManager.php');
require_once('model/CommentManager.php');

function listPosts()
{
    $postManager = new \OpenClassrooms\Blog\Model\PostManager();
    $posts = $postManager->getPosts();

    require('view/frontend/listPostsView.php');
}

function post()
{
    $postManager = new \OpenClassrooms\Blog\Model\PostManager();
    $commentManager = new \OpenClassrooms\Blog\Model\CommentManager();

    $post = $postManager->getPost($_GET['id']);
    $comments = $commentManager->getComments($_GET['id']);

    require('view/frontend/postView.php');
}

function addComment($postId, $author, $comment)
{
    $commentManager = new \OpenClassrooms\Blog\Model\CommentManager();
    $affectedLines = $commentManager->postComment($postId, $author, $comment);

    if ($affectedLines === false) {
        throw new Exception('Impossible d\'ajouter le commentaire !');
    } else {
        header('Location: index.php?action=post&id=' . $postId);
    }
}

function printComment($id) {
    $commentManager = new \OpenClassrooms\Blog\Model\CommentManager();
    $comment = $commentManager->getComment($id); 
    
    require('view/frontend/commentView.php'); 
}

function editComment($id, $comment, $postId) {
    $commentManager = new \OpenClassrooms\Blog\Model\CommentManager();
    $newComment = $commentManager->updateComment($id, $comment);

    if ($newComment === false) {
        throw new Exception('Impossible de modifier ce commentaire !');
    } else {
        // echo 'Commentaire : ' . $_POST['comment'];
        header('Location: index.php?action=post&id=' . $postId);
    }
}

function reportComment($id, $postId) {
    $commentManager = new \OpenClassrooms\Blog\Model\CommentManager();
    $reportedComment = $commentManager->reportComment($id);

    if ($reportedComment === false) {
        throw new Exception('Impossible de signaler ce commentaire !');
    } else {
        // echo 'Commentaire : ' . $_POST['comment'];
        header('Location: index.php?action=post&id=' . $postId);
    }
}

function deleteComment($id, $postId) {
    $commentManager = new \OpenClassrooms\Blog\Model\CommentManager();
    $deletedComment = $commentManager->deleteComment($id);

    if ($deletedComment === false) {
        throw new Exception('Impossible de supprimer ce commentaire !');
    } else {
        header('Location: index.php?action=post&id=' . $postId);
    }
}

function newPost() {
    $postManager = new \OpenClassrooms\Blog\Model\PostManager();

    require('view/frontend/newPostView.php');
}

function addPost($title, $content) {
    $postManager = new \OpenClassrooms\Blog\Model\PostManager();
    $postContent = $postManager->postUpload($title, $content);

    header('Location: index.php');
}

function printPost($postId) {
    $postManager = new \OpenClassrooms\Blog\Model\PostManager();
    $post = $postManager->getPost($postId);

    require('view/frontend/editPostView.php');
}

function editPost($postId, $title, $content) {
    $postManager = new \OpenClassrooms\Blog\Model\PostManager();
    $modifiedPost = $postManager->updatePost($postId, $title, $content);

    if ($modifiedPost === false) {
        // throw new Exception('Impossible de modidier ce post');
        echo $postId;
    } else {
        header('Location: index.php');
    }
}

function deletePost($postId) {
    $postManager = new \OpenClassrooms\Blog\Model\PostManager();
    $deletedPost = $postManager->deletePost($postId);

    if ($deletedPost === false) {
        throw new Exception('Impossible de supprimer ce post');
    } else {
        header('Location: index.php');
    }
}