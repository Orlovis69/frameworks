const buttonsFav = document.querySelectorAll('.favorite-button'),
    buttonsUnFav = document.querySelectorAll('.unfavorite-button');

function favorite() {
    // Get blog-post div element
    const parent = this.parentElement;

    // ajax request
    const http = new XMLHttpRequest;
    http.open('POST', 'favorite.php', true);
    // Set headers
    http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    http.setRequestHeader('X-Requested-with', 'XMLHttpRequest');
    // Callback function
    http.onreadystatechange = function() {
        if(this.readyState == 4 && this.status == 200) {
            const result = this.responseText;
            // Change class of parent element if it is favorite
            if(result == 'true') {
                parent.classList.add('favorite');
            }

            console.log(result);
        }
    }
    // Send request
    http.send("id=" + parent.id);
}

function unfavorite() {
    // Get blog-post div element
    const parent = this.parentElement;

    // ajax request
    const http = new XMLHttpRequest;
    http.open('POST', 'unfavorite.php', true);
    // Set headers
    http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    http.setRequestHeader('X-Requested-with', 'XMLHttpRequest');
    // Callback function
    http.onreadystatechange = function() {
        if(this.readyState == 4 && this.status == 200) {
            const result = this.responseText;
            // Change class of parent element if it is favorite
            if(result == 'true') {
                parent.classList.remove('favorite');
            }

            console.log(result);
        }
    }
    // Send request
    http.send("id=" + parent.id);
}

// Listener for all buttons
buttonsFav.forEach(button => button.addEventListener('click', favorite));
buttonsUnFav.forEach(button => button.addEventListener('click', unfavorite));
