
// Get the navigation bar
const navbar = document.getElementById('navbar');

// Get the offset position of the navbar
const sticky = navbar.offsetTop;

// Add an event listener for scroll
window.addEventListener('scroll', () => {
    if (window.pageYOffset > sticky) {
        navbar.classList.add('sticky');
    } else {
        navbar.classList.remove('sticky');
    }
});


//slider
$(document).ready(function () {
    // Function to set equal heights
    function setEqualHeight() {
        const cards = $('.owl-carousel .card');
        let maxHeight = 0;

        // Reset height
        cards.css('height', 'auto');

        // Find the maximum height
        cards.each(function () {
            maxHeight = Math.max(maxHeight, $(this).outerHeight());
        });

        // Set all cards to the maximum height
        cards.css('height', maxHeight + 'px');
    }

    // Initialize Owl Carousel
    $('.owl-carousel').owlCarousel({
        loop: true,
        margin: 10,
        nav: true,
        autoplay: true,
        autoplayTimeout: 3000,
        responsive: {
            0: {
                items: 1,
                slideBy: 1 // Slide one card at a time
            },
            600: {
                items: 2,
                slideBy: 1 // Slide one card at a time
            },
            1000: {
                items: 4,
                slideBy: 1 // Slide one card at a time
            }
        },
        // Call setEqualHeight after initialization and resize
        onInitialized: setEqualHeight,
        onResized: setEqualHeight
    });
});

// Get the modal
var modal = document.getElementById("loginModal");

// Get the button that opens the modal
var btn = document.getElementById("loginBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal 
btn.onclick = function () {
    modal.classList.remove("hidden");
}

// When the user clicks on <span> (x), close the modal
span.onclick = function () {
    modal.classList.add("hidden");
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function (event) {
    if (event.target == modal) {
        modal.classList.add("hidden");
    }
}

// Prevent form submission for demonstration purposes
document.getElementById("loginForm").onsubmit = function (event) {
    event.preventDefault();
    alert("Form submitted!");
    modal.classList.add("hidden"); // Close modal after submission
}
// Toggle search bar visibility
document.getElementById('toggleSearch').onclick = function () {
    const searchBar = document.getElementById('searchBar');
    searchBar.classList.toggle('hidden');
};

// Modal code (from previous implementation)
var modal = document.getElementById("loginModal");
var btn = document.getElementById("loginBtn");
var span = document.getElementsByClassName("close")[0];

btn.onclick = function () {
    modal.classList.remove("hidden");
};

span.onclick = function () {
    modal.classList.add("hidden");
};

window.onclick = function (event) {
    if (event.target == modal) {
        modal.classList.add("hidden");
    }
};

document.getElementById("loginForm").onsubmit = function (event) {
    event.preventDefault();
    alert("Form submitted!");
    modal.classList.add("hidden"); // Close modal after submission
};


// Mobile Menu Toggle
const mobileMenuButton = document.getElementById('mobileMenuButton');
const mobileMenu = document.getElementById('mobileMenu');

mobileMenuButton.addEventListener('click', () => {
    mobileMenu.classList.toggle('hidden');
});

// Toggle search bar visibility
const toggleSearch = document.getElementById('toggleSearch');
const searchBar = document.getElementById('searchBar');

toggleSearch.addEventListener('click', () => {
    searchBar.classList.toggle('hidden');
});

// Login form toggle
const loginBtn = document.getElementById('loginBtn');
const mobileLoginBtn = document.getElementById('mobileLoginBtn');
const loginForm = document.getElementById('loginForm');
const closeLoginForm = document.getElementById('closeLoginForm');

loginBtn.addEventListener('click', () => {
    loginForm.classList.toggle('hidden');
});

mobileLoginBtn.addEventListener('click', () => {
    loginForm.classList.toggle('hidden');
});

closeLoginForm.addEventListener('click', () => {
    loginForm.classList.add('hidden');
});
// JavaScript to handle the search bar toggle
document.getElementById('toggleSearch').addEventListener('click', function () {
    const searchBar = document.getElementById('searchBar');
    searchBar.classList.toggle('hidden');
});

// JavaScript to handle search action
document.getElementById('searchInput').addEventListener('keypress', function (e) {
    if (e.key === 'Enter') {
        const query = this.value.trim();
        if (query) {
            // Here you can define what to do with the search query, for example:
            alert('Searching for: ' + query);
            // You can redirect to a search results page or filter items on the current page
            // window.location.href = '/search-results.html?query=' + encodeURIComponent(query);
        }
    }
});
// JavaScript to handle the search bar toggle
document.getElementById('toggleSearch').addEventListener('click', function () {
    const searchBar = document.getElementById('searchBar');
    searchBar.classList.toggle('hidden'); // Toggle visibility on click
});

// JavaScript to handle search action
document.getElementById('searchInput').addEventListener('keypress', function (e) {
    if (e.key === 'Enter') {
        const query = this.value.trim();
        if (query) {
            // Here you can define what to do with the search query
            alert('Searching for: ' + query);
            // window.location.href = '/search-results.html?query=' + encodeURIComponent(query);
        }
    }
});

// Hide the search bar on click outside (for larger screens)
document.addEventListener('click', function (event) {
    const searchBar = document.getElementById('searchBar');
    const toggleSearchButton = document.getElementById('toggleSearch');
    const isClickInside = searchBar.contains(event.target) || toggleSearchButton.contains(event.target);

    if (!isClickInside && !searchBar.classList.contains('hidden')) {
        searchBar.classList.add('hidden');
    }
});
