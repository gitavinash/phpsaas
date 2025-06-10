$(document).ready(function () {
  if ($("#galleryModal").length === 0) return;

  const $modal = $("#galleryModal");
  const $modalImage = $("#modalImage");
  const $modalTitle = $("#modalTitle");
  const $modalDescription = $("#modalDescription");
  const $closeModal = $("#closeModal");
  const $prevButton = $("#prevImage");
  const $nextButton = $("#nextImage");

  const galleryData = [
    {
      src: "https://readdy.ai/api/search-image?query=before%2520and%2520after%2520hair%2520transplant%2520male%2520patient%252C%2520side%2520view%2520of%2520balding%2520crown%2520area%2520before%2520treatment%2520and%2520full%2520hair%2520coverage%2520after%2520treatment%252C%2520same%2520lighting%2520conditions%252C%2520neutral%2520background%252C%2520clinical%2520photography%2520style&width=400&height=300&seq=3&orientation=landscape",
      title: "Crown Restoration",
      description: "6 months post-procedure",
    },
    {
      src: "https://readdy.ai/api/search-image?query=before%2520and%2520after%2520hair%2520transplant%2520male%2520patient%252C%2520front%2520view%2520of%2520receding%2520hairline%2520before%2520treatment%2520and%2520restored%2520hairline%2520after%2520treatment%252C%2520same%2520lighting%2520conditions%252C%2520neutral%2520background%252C%2520clinical%2520photography%2520style&width=400&height=300&seq=4&orientation=landscape",
      title: "Hairline Reconstruction",
      description: "8 months post-procedure",
    },
    {
      src: "https://readdy.ai/api/search-image?query=before%2520and%2520after%2520hair%2520transplant%2520male%2520patient%252C%2520top%2520view%2520of%2520thinning%2520hair%2520before%2520treatment%2520and%2520dense%2520hair%2520coverage%2520after%2520treatment%252C%2520same%2520lighting%2520conditions%252C%2520neutral%2520background%252C%2520clinical%2520photography%2520style&width=400&height=300&seq=5&orientation=landscape",
      title: "Overall Density Improvement",
      description: "12 months post-procedure",
    },
    {
      src: "https://readdy.ai/api/search-image?query=before%2520and%2520after%2520hair%2520transplant%2520female%2520patient%252C%2520front%2520view%2520of%2520thinning%2520hair%2520at%2520part%2520line%2520before%2520treatment%2520and%2520thicker%2520hair%2520after%2520treatment%252C%2520same%2520lighting%2520conditions%252C%2520neutral%2520background%252C%2520clinical%2520photography%2520style&width=400&height=300&seq=6&orientation=landscape",
      title: "Female Pattern Hair Loss",
      description: "9 months post-procedure",
    },
  ];

  let currentIndex = 0;

  function showImage(index) {
    currentIndex = index;
    $modalImage.attr("src", galleryData[index].src);
    $modalTitle.text(galleryData[index].title);
    $modalDescription.text(galleryData[index].description);
  }

  function showNextImage() {
    currentIndex = (currentIndex + 1) % galleryData.length;
    showImage(currentIndex);
  }

  function showPrevImage() {
    currentIndex = (currentIndex - 1 + galleryData.length) % galleryData.length;
    showImage(currentIndex);
  }

  $(".gallery-image").on("click", function () {
    const index = parseInt($(this).data("index"));
    showImage(index);
    $modal.removeClass("hidden");
  });

  $closeModal.on("click", function () {
    $modal.addClass("hidden");
  });

  $prevButton.on("click", showPrevImage);
  $nextButton.on("click", showNextImage);

  $(document).on("keydown", function (e) {
    if ($modal.hasClass("hidden")) return;

    if (e.key === "Escape") $modal.addClass("hidden");
    if (e.key === "ArrowLeft") showPrevImage();
    if (e.key === "ArrowRight") showNextImage();
  });
});
