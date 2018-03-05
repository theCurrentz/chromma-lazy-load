function lazyLoadController() {
    //lazyloading images
    var allimages = document.getElementsByClassName("llreplace");
    if (allimages.length > 0) {
    for (var i = 0, j = allimages.length; i < j; i++) {
      if (allimages[0].length > 0) {
        allimages[0].addEventListener("load", lazyLoadExecutionScroll());
      }
    }
    window.addEventListener("scroll", function() {
        lazyLoadExecutionScroll();
    });
    }

    //fire when dom content is loaded
    document.addEventListener("DOMContentLoaded", function() {
      lazyLoadExecutionScroll()
    });

    //execute lazy load
    function lazyLoadExecutionScroll() {
        var imgCount = 0;
        while (imgCount < allimages.length) {
            if (inView(allimages[imgCount])) {
                lazyLoadImage(allimages[imgCount]);
                allimages[imgCount].classList.remove("llreplace");
            } else {
                imgCount++;
            }
        }
    }
}

//discover if the lazyload target element is in view
function inView(element) {
    var elementSize = element.getBoundingClientRect();
    var html = document.documentElement;
    return (
        elementSize.top >= 0 &&
        elementSize.left >= -100 &&
        elementSize.bottom <= 620 + (window.innerHeight || html.clientHeight) &&
        elementSize.right <= 2640 + (window.innerWidth || html.clientWidth)
    );
}

//replace src w/ data src and animate image in
function lazyLoadImage(element)
{
    if (element.getAttribute("data-src"))
    {
        element.setAttribute("src", element.getAttribute("data-src"));
        element.removeAttribute("data-src");
    }
    if (element.getAttribute("data-srcset"))
    {
        element.setAttribute("srcset", element.getAttribute("data-srcset"));
        element.removeAttribute("data-srcset");
    }
    //use below code if using a container e.g. filter
    // element.style.position = "relative";
    // element.style.height = "auto";
    // element.parentNode.style.height = "auto";
    // element.parentNode.style.paddingBottom = "0px";
    if(element.classList)
      element.classList.add("reveal");
}

lazyLoadController();
