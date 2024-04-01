const rangeInputs = document.querySelectorAll(".input-range input"),
    priceInputs = document.querySelectorAll(".input-price input"),
    ranges = document.querySelector(".slider-range .progress-bar");
let priceGaps = 1000;
priceInputs.forEach(input => {
    input.addEventListener("input", e => {
        let minPrice = parseInt(priceInputs[0].value),
            maxPrice = parseInt(priceInputs[1].value);

        if ((maxPrice - minPrice >= priceGaps) && maxPrice <= rangeInputs[1].max) {
            if (e.target.className === "input-min") {
                rangeInputs[0].value = minPrice;
                ranges.style.left = ((minPrice / rangeInputs[0].max) * 100) + "%";
            } else {
                rangeInputs[1].value = maxPrice;
                ranges.style.right = 100 - (maxPrice / rangeInputs[1].max) * 100 + "%";
            }
        }
    });
});
rangeInputs.forEach(input => {
    input.addEventListener("input", e => {
        let minVal = parseInt(rangeInputs[0].value),
            maxVal = parseInt(rangeInputs[1].value);
        if ((maxVal - minVal) < priceGaps) {
            if (e.target.className === "range-min") {
                rangeInputs[0].value = maxVal - priceGaps
            } else {
                rangeInputs[1].value = minVal + priceGaps;
            }
        } else {
            priceInputs[0].value = minVal;
            priceInputs[1].value = maxVal;
            ranges.style.left = ((minVal / rangeInputs[0].max) * 100) + "%";
            ranges.style.right = 100 - (maxVal / rangeInputs[1].max) * 100 + "%";
        }
    });
});



