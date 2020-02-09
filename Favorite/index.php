<?php
session_start();

// clear $_SESSION
// $_SESSION['favorites'] = [];

if (!isset($_SESSION['favorites'])) {
    $_SESSION['favorites'] = [];
}

function is_favorite($id)
{
    return in_array($id, $_SESSION['favorites']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Asynchronous button</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <section id="blog-posts">
        <div id="blog-post-101" class="blog-post <?php if (is_favorite('101')) {
                                                        echo 'favorite';
                                                    }; ?>">
            <span class="favorite-heart">&hearts;</span>
            <h3>Blog Post 101</h3>
            <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Delectus nostrum officiis labore cupiditate expedita incidunt perspiciatis, reiciendis deserunt ratione porro, eaque perferendis voluptatibus optio, dignissimos voluptate fugiat est. Ipsam, deleniti?</p>
            <button class="favorite-button">Favorite</button>
            <button class="unfavorite-button">Unfavorite</button>
        </div>
        <div id="blog-post-102" class="blog-post <?php if (is_favorite('102')) {
                                                        echo 'favorite';
                                                    }; ?>">
            <span class="favorite-heart">&hearts;</span>
            <h3>Blog Post 102</h3>
            <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Delectus nostrum officiis labore cupiditate expedita incidunt perspiciatis, reiciendis deserunt ratione porro, eaque perferendis voluptatibus optio, dignissimos voluptate fugiat est. Ipsam, deleniti?</p>
            <button class="favorite-button">Favorite</button>
            <button class="unfavorite-button">Unfavorite</button>
        </div>
        <div id="blog-post-103" class="blog-post <?php if (is_favorite('103')) {
                                                        echo 'favorite';
                                                    }; ?>">
            <span class="favorite-heart">&hearts;</span>
            <h3>Blog Post 103</h3>
            <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Delectus nostrum officiis labore cupiditate expedita incidunt perspiciatis, reiciendis deserunt ratione porro, eaque perferendis voluptatibus optio, dignissimos voluptate fugiat est. Ipsam, deleniti?</p>
            <button class="favorite-button">Favorite</button><button class="unfavorite-button">Unfavorite</button>
        </div>
    </section>

</body>
<script src="favorite.js"></script>

</html>