const categories = document.querySelector('#category-select'),
    subcategories = document.querySelector('#subcategory-select');

function updateSubcategories() {
    // Get the id of selected category
    const catId = categories.options[categories.selectedIndex].value;
    // Create URL for get request
    const url = 'subcategories.php?category_id=' + catId;
    
    // Fetching Data
    const http = new XMLHttpRequest();
    http.open('GET', url, true);
    http.onreadystatechange = function() {
        if(http.readyState === 4 && http.status === 200) {
            subcategories.innerHTML = this.responseText;
            subcategories.style.display = "inline-block";
        }
    }
    http.send();
}

// Listen for change of category
categories.addEventListener('change', updateSubcategories);