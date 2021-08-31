(function ($) {
  /* ===============================================================
    Mobile Menu Feature
  =============================================================== */
  class MobileMenu {
    constructor() {
      this.menu = document.querySelector(".site-header__menu");
      this.openButton = document.querySelector(".site-header__menu-trigger");
      this.events();
    }

    events() {
      this.openButton.addEventListener("click", () => this.openMenu());
    }

    openMenu() {
      this.openButton.classList.toggle("fa-bars");
      this.openButton.classList.toggle("fa-window-close");
      this.menu.classList.toggle("site-header__menu--active");
    }
  }

  new MobileMenu();

  /* ===============================================================
    Hero Slider / Homepage Slideshow
  =============================================================== */
  class HeroSlider {
    constructor() {
      if (document.querySelector(".hero-slider")) {
        // count how many slides there are
        const dotCount = document.querySelectorAll(".hero-slider__slide").length;

        // Generate the HTML for the navigation dots
        let dotHTML = "";
        for (let i = 0; i < dotCount; i++) {
          dotHTML += `<button class="slider__bullet glide__bullet" data-glide-dir="=${i}"></button>`;
        }

        // Add the dots HTML to the DOM
        document.querySelector(".glide__bullets").insertAdjacentHTML("beforeend", dotHTML);

        // Actually initialize the glide / slider script
        var glide = new Glide(".hero-slider", {
          type: "carousel",
          perView: 1,
          autoplay: 3000,
        });

        glide.mount();
      }
    }
  }

  new HeroSlider();

  /* ===============================================================
    Google Map / Campus Map
  =============================================================== */
  class GoogleMap {
    constructor() {
      document.querySelectorAll(".acf-map").forEach((el) => {
        this.new_map(el);
      });
    }

    new_map($el) {
      var $markers = $el.querySelectorAll(".marker");

      var args = {
        zoom: 16,
        center: new google.maps.LatLng(0, 0),
        mapTypeId: google.maps.MapTypeId.ROADMAP,
      };

      var map = new google.maps.Map($el, args);
      map.markers = [];
      var that = this;

      // add markers
      $markers.forEach(function (x) {
        that.add_marker(x, map);
      });

      // center map
      this.center_map(map);
    } // end new_map

    add_marker($marker, map) {
      var latlng = new google.maps.LatLng($marker.getAttribute("data-lat"), $marker.getAttribute("data-lng"));

      var marker = new google.maps.Marker({
        position: latlng,
        map: map,
      });

      map.markers.push(marker);

      // if marker contains HTML, add it to an infoWindow
      if ($marker.innerHTML) {
        // create info window
        var infowindow = new google.maps.InfoWindow({
          content: $marker.innerHTML,
        });

        // show info window when marker is clicked
        google.maps.event.addListener(marker, "click", function () {
          infowindow.open(map, marker);
        });
      }
    } // end add_marker

    center_map(map) {
      var bounds = new google.maps.LatLngBounds();

      // loop through all markers and create bounds
      map.markers.forEach(function (marker) {
        var latlng = new google.maps.LatLng(marker.position.lat(), marker.position.lng());

        bounds.extend(latlng);
      });

      // only 1 marker?
      if (map.markers.length == 1) {
        // set center of map
        map.setCenter(bounds.getCenter());
        map.setZoom(16);
      } else {
        // fit to bounds
        map.fitBounds(bounds);
      }
    } // end center_map
  }

  new GoogleMap();

  /* ===============================================================
    Search / Live Overlay Results
  =============================================================== */
  class Search {
    // 1. describe and create/initiate our object
    constructor() {
      this.addSearchHTML();
      this.openButton = $(".js-search-trigger");
      this.closeButton = $(".search-overlay__close");
      this.searchOverlay = $(".search-overlay");
      this.searchField = $("#search-term");
      this.resultDiv = $("#search-overlay__results");
      this.isOverlayOpen = false;
      this.isSpinnerVisible = false;
      this.typingTimer;
      this.previousValue;
      this.events();
    }

    // 2. events
    events() {
      this.openButton.on("click", this.openOverlay.bind(this));
      this.closeButton.on("click", this.closeOverlay.bind(this));
      $(document).on("keydown", this.keyPressDispatcher.bind(this));
      this.searchField.on("keyup", this.typingLogic.bind(this));
    }

    // 3. methods (function, action...)
    typingLogic() {
      if (this.searchField.val() != this.previousValue) {
        clearTimeout(this.typingTimer);

        if (this.searchField.val()) {
          if (!this.isSpinnerVisible) {
            this.resultDiv.html('<div class="spinner-loader"></div>');
            this.isSpinnerVisible = true;
          }
          this.typingTimer = setTimeout(this.getResults.bind(this), 700);
        } else {
          this.resultDiv.html("");
          this.isSpinnerVisible = false;
        }
      }

      this.previousValue = this.searchField.val();
    }

    getResults() {
      $.when(
        $.getJSON(universityData.root_url + "/wp-json/wp/v2/posts?search=" + this.searchField.val()),
        $.getJSON(universityData.root_url + "/wp-json/wp/v2/pages?search=" + this.searchField.val())
      ).then(
        (posts, pages) => {
          let combinedResults = posts[0].concat(pages[0]);
          this.resultDiv.html(`
        <h2 class="search-overlay__section-title">General Information</h2>
        ${combinedResults.length ? '<ul class="link-list min-list">' : "<p>No general information matches that search.</p>"}
          ${combinedResults.map((item) => `<li><a href="${item.link}">${item.title.rendered}</a>${item.type == 'post' ? ` by ${item.authorName}` : ''}</li>`).join("")}
        ${combinedResults.length ? "</ul>" : ""}
      `);
          this.isSpinnerVisible = false;
        },
        () => {
          this.resultDiv.html("<p>Unexpected Error, Please try again.</p>");
        }
      );
    }

    keyPressDispatcher(e) {
      if (e.keyCode == 83 && !this.isOverlayOpen && !$("input, textarea").is(":focus")) {
        this.openOverlay();
      }
      if (e.keyCode == 27 && this.isOverlayOpen) {
        this.closeOverlay();
      }
    }

    openOverlay() {
      this.searchOverlay.addClass("search-overlay--active");
      $("body").addClass("body-no-scroll");
      this.searchField.val("");
      setTimeout(() => {
        this.searchField.focus();
      }, 301);
      this.isOverlayOpen = true;
      console.log("openOverlay run");
    }

    closeOverlay() {
      this.searchOverlay.removeClass("search-overlay--active");
      $("body").removeClass("body-no-scroll");
      this.isOverlayOpen = false;
      console.log("closeOverlay run");
    }

    addSearchHTML() {
      $("body").append(`
        <div class="search-overlay">
          <div class="search-overlay__top">
            <div class="container">
              <i class="fa fa-search search-overlay__icon" aria-hidden="true"></i>
              <input type="text" class="search-term" placeholder="What are you looking for?" id="search-term">
              <i class="fa fa-window-close search-overlay__close" aria-hidden="true"></i>
            </div>
          </div>
          <div class="container">
            <div id="search-overlay__results"></div>
          </div>
        </div>
      `);
    }
  }

  new Search();

  /* ===============================================================
    My Notes Feature
  =============================================================== */
  class MyNotes {}

  new MyNotes();

  /* ===============================================================
    Like a Professor Feature
  =============================================================== */
  class Like {}

  new Like();
})(jQuery);
