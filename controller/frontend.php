<?php

// Chargement des classes
require_once('model/PostManager.php');
require_once('model/CommentManager.php');
require_once('model/UserManager.php');

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
    $nbComments = $commentManager->getCommentCount($_GET['id']);

    if ($post) {
        require('view/frontend/postView.php');
    } else {
        throw new Exception('Cette page n\'existe pas');
    }
}

function addComment($postId, $pseudo, $comment)
{
    $commentManager = new \OpenClassrooms\Blog\Model\CommentManager();
    $affectedLines = $commentManager->postComment($postId, $pseudo, $comment);

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

function deleteCommentAdmin($id) {
    $commentManager = new \OpenClassrooms\Blog\Model\CommentManager();
    $postManager = new \OpenClassrooms\Blog\Model\PostManager();
    $deletedComment = $commentManager->deleteComment($id);
    $posts = $postManager->getPosts();
    $comments = $commentManager->getFlagComments();

    if ($deletedComment === false) {
        throw new Exception('Impossible de supprimer ce commentaire !');
    } else {
        require('view/frontend/admin.php');
    }
}

function resetReportCount($id) {
    $commentManager = new \OpenClassrooms\Blog\Model\CommentManager();
    $postManager = new \OpenClassrooms\Blog\Model\PostManager();
    $resetedComment = $commentManager->resetReportCount($id);
    $posts = $postManager->getPosts();
    $comments = $commentManager->getFlagComments();

    if ($resetedComment === false) {
        throw new Exception('Impossible de reset ce commentaire !');
    } else {
        require('view/frontend/admin.php');
    }
}

function newPost() {
    require('view/frontend/newPostView.php');
}

function addPost($title, $content) {
    $postManager = new \OpenClassrooms\Blog\Model\PostManager();
    $commentManager = new \OpenClassrooms\Blog\Model\CommentManager();
    $postContent = $postManager->postUpload($title, $content);
    $posts = $postManager->getPosts();
    $comments = $commentManager->getFlagComments();

    require('view/frontend/admin.php');
}

function printPost($postId) {
    $postManager = new \OpenClassrooms\Blog\Model\PostManager();
    $commentManager = new \OpenClassrooms\Blog\Model\CommentManager();
    $post = $postManager->getPost($postId);
    $comments = $commentManager->getComments($postId);
    $nbComments = $commentManager->getCommentCount($postId);

    require('view/frontend/editPostView.php');
}

function editPost($postId, $title, $content) {
    $postManager = new \OpenClassrooms\Blog\Model\PostManager();
    $commentManager = new \OpenClassrooms\Blog\Model\CommentManager();
    $modifiedPost = $postManager->updatePost($postId, $title, $content);
    $posts = $postManager->getPosts();
    $comments = $commentManager->getFlagComments();

    if ($modifiedPost === false) {
        // throw new Exception('Impossible de modidier ce post');
        echo $postId;
    } else {
        require('view/frontend/admin.php');
    }
}

function deletePost($postId) {
    $postManager = new \OpenClassrooms\Blog\Model\PostManager();
    $commentManager = new \OpenClassrooms\Blog\Model\CommentManager();
    $deletedPost = $postManager->deletePost($postId);
    $posts = $postManager->getPosts();
    $comments = $commentManager->getFlagComments();

    if ($deletedPost === false) {
        throw new Exception('Impossible de supprimer ce post');
    } else {
        require('view/frontend/admin.php');
    }
}

function login() {
    require('view/frontend/userLogin.php');
}

function logout() {
    header('Location: index.php');
}

function adminArea() {
    $postManager = new \OpenClassrooms\Blog\Model\PostManager();
    $commentManager = new \OpenClassrooms\Blog\Model\CommentManager();
    $posts = $postManager->getPosts();
    $comments = $commentManager->getFlagComments();

    if (checkAdmin()) {
        require('view/frontend/admin.php');
    }
}

function register() {
    require('view/frontend/register.php');
}

function registerUser($pseudo, $password) {
    $userManager = new \OpenClassrooms\Blog\Model\UserManager();
    $passHash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $notUnique = $userManager->checkPseudo($pseudo);

        if ($notUnique) {
            throw new Exception('Pseudo déjà prit !');
        } else {
            header('Location: index.php');
            // echo 'Vous êtes maintenant inscrits !';
            $userManager->register($pseudo, $passHash);
        }
}

function loginUser($pseudo, $password) {
    $userManager = new \OpenClassrooms\Blog\Model\UserManager();
    $check = $userManager->getPass($pseudo);
    $samePass = password_verify($_POST['password'], $check['password']);

    if (!$check) {
        echo 'Mauvais mot de passe ou identifiant !';
    } else {
        if ($samePass) {
            $_SESSION['id'] = $check['id'];
            $_SESSION['role'] = $check['name'];
            $_SESSION['pseudo'] = $pseudo;
            
            header('Location: index.php');
        } else {
            echo 'Mauvais mot de passe ou identifiant !';
        }
    }
}

function checkAdmin() {
    if (!$_SESSION['role'] == 'admin') {
        echo 'Vous n\'êtes pas autorisés à accéder à cette page !';
        require('view/frontend/userLogin.php');
    } else {
        return true;
    }
}