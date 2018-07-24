// fire when dom content is loaded
if(document.getElementsByClassName('llreplace').length > 0) {
  var allimages = document.getElementsByClassName('llreplace')
  allimages[allimages.length - 1].addEventListener('load', lazyLoadController())
}

function lazyLoadController () {
  // lazyloading images
  var allimages = document.getElementsByClassName('llreplace')
  lazyLoadExecution() //fire once before scroll listener
  if (allimages.length > 0) {
    window.addEventListener('scroll', function () {
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

}

// discover if the lazyload target element is in view
function inView (element) {
  var elementSize = element.getBoundingClientRect()
  var html = document.documentElement
  return (
    elementSize.top >= 0 &&
        elementSize.left >= -100 &&
        elementSize.bottom <= 620 + (window.innerHeight || html.clientHeight) &&
        elementSize.right <= 2640 + (window.innerWidth || html.clientWidth)
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
