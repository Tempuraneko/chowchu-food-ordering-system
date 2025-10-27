(function ($) {
    $(document).ready(function () {
        // Toggle Sidebar on Menu Button Click
        $("#menu-btn").click(function () {
            $(".side-bar").toggleClass("active");
            $("body").toggleClass("sidebar-active");
            // Update body padding based on sidebar state
            if ($(".side-bar").hasClass("active")) {
                $("body").css("padding-left", "0");
            } else {
                $("body").css("padding-left", "30rem");
            }
        });
 
        // Close Sidebar on Close Button Click
        $(".close-side-bar i").click(function () {
            $(".side-bar").toggleClass("active");
            $("body").toggleClass("sidebar-active");
            // Update body padding based on sidebar state
            if ($(".side-bar").hasClass("active")) {
                $("body").css("padding-left", "0");
            } else {
                $("body").css("padding-left", "30rem");
            }
        });
 
        // Close Sidebar on Esc Key
        $(document).keydown(function (event) {
            if (event.key === "Escape") {
                $(".side-bar").removeClass("active");
                $("body").removeClass("sidebar-active");
            }
        }); 

       // Toggle Profile Dropdown
       $("#user-btn").click(function () {
           $(".profile").toggleClass("active");
       });

       $(document).ready(function() {
        // Toggle Filter Dropdown
        $("#filter-btn").click(function() {
            $(".filter-category").toggleClass("active");
        });
    
        // Close Filter Dropdown if clicked outside
        $(document).click(function(event) {
            if (!$(event.target).closest(".filter-category, #filter-btn").length) {
                $(".filter-category").removeClass("active");
            }
        });
    });
    
       // Close Profile on Click Outside
       $(document).click(function (event) {
           if (!$(event.target).closest(".profile, #user-btn").length) {
               $(".profile").removeClass("active");
           }
       });
   });

   
})(jQuery);

// Admin Header Dropdown list
$(document).ready(function () {

    $("#user-btn").on("click", function () {
        $(".user-dropdown .dropdown-menu").toggle();
    });

    $(document).on("click", function (e) {
        if (!$(e.target).closest(".user-dropdown").length) {
            $(".user-dropdown .dropdown-menu").hide();
        }
    });
});

$(document).ready(function () {
    var modal = $("#imageModal");

    $(".product-img").click(function () {
        var imgSrc = $(this).attr("src"); 
        $("#popimg").attr("src", imgSrc);
    
        modal.show();
    });

    $(".close").click(function () {
        modal.hide();
    });

    $(window).click(function (event) {
        if (event.target == modal[0]) {
            modal.hide(); 
        }
    });
});


$(document).ready(function () {
    $("#select-all").click(function () {
        $("input[name='deleteId[]']").prop("checked", this.checked);
    });
});

