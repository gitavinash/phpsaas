const detailsBox = document.querySelectorAll(".click-filter");
detailsBox.forEach((el) => {
  el.addEventListener("click", () => {
    close();
    const show = document.querySelector(`#show${el.id}`);
    show.classList.add("show");
  });
});

function close() {
  const removeShow = document.querySelectorAll(".mobile-dropdown");
  removeShow.forEach((el) => el.classList.remove("show"));
}

const filterSection = document.querySelector(".filter-section");
const filteropen = document.querySelector(".filterBar");
const closefilter = document.querySelector(".filterclose");

filteropen.addEventListener("click", () => {
  filterSection.classList.add("active");
});

closefilter.addEventListener("click", () => {
  filterSection.classList.remove("active");
  // console.log("hi");
});

console.log(closefilter);
