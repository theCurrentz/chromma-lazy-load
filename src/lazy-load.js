const lazyLoadController = function() {
  // lazyloading images
  var allimages = document.getElementsByClassName('llreplace')
  if (allimages.length > 0) {
    //execution conditions
    if (document.readyState === "loading") {
      document.addEventListener("DOMContentLoaded", () => {lazyLoadExecution()});
    }
    window.addEventListener('scroll', () => {
      lazyLoadExecution()
    })
  }

  // execute lazy load
  function lazyLoadExecution() {
    var imgCount = 0
    while (imgCount < allimages.length) {
      if (inView(allimages[imgCount])) {
        lazyLoadImage(allimages[imgCount])
        allimages[imgCount].classList.remove('llreplace')
      } else {
        imgCount++
      }
    }
  }
  // discover if the lazyload target element is in view
  function inView (element) {
    var elementSize = element.getBoundingClientRect()
    var html = document.documentElement
    return (
      elementSize.top >= 0 &&
      elementSize.bottom <= 840 + (window.innerHeight || html.clientHeight)
    )
  }

  // replace src w/ data src and animate image in
  function lazyLoadImage (element) {
    if (element.getAttribute('data-srcset')) {
      element.setAttribute('srcset', element.getAttribute('data-srcset'))
      element.removeAttribute('data-srcset')
    }
    if (element.getAttribute('data-src')) {
      element.setAttribute('src', element.getAttribute('data-src'))
      element.removeAttribute('data-src')
    }

    if (element.classList) { element.classList.add('reveal') }
  }

}
const lazyLoader = new lazyLoadController()
