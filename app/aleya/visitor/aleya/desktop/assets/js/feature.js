var boxes = document.querySelectorAll('.feature_content__box');
var answers = document.querySelectorAll('.boxContainer');

// Add 'active' class to the first answer element
answers[0].classList.add('active');

// Loop through each box element
for (var i = 0; i < boxes.length; i++) {
    boxes[i].addEventListener('click', function() {
        // Inside the event listener, "this" refers to the current box element being clicked
        // Find the corresponding answer element using the index of the box element
        var index = Array.prototype.indexOf.call(boxes, this);
        var answer = answers[index];
        
        // Remove the 'active' class from all answer elements
        for (var j = 0; j < answers.length; j++) {
            answers[j].classList.remove('active');
        }
        
        // Toggle the 'active' class for the corresponding answer element
        answer.classList.toggle('active');
    });
}

// second section
var boxesTwo = document.querySelectorAll('.box_wrapper');
var answersTwo = document.querySelectorAll('.box_ans');

// Add 'active' class to the first answer element
answersTwo[0].classList.add('show');

// Loop through each box element
for (var k = 0; k < boxesTwo.length; k++) {
    boxesTwo[k].addEventListener('click', function() {
        // Inside the event listener, "this" refers to the current box element being clicked
        // Find the corresponding answer element using the index of the box element
        var index = Array.prototype.indexOf.call(boxesTwo, this);
        var myanswer = answersTwo[index];
        
        // Remove the 'active' class from all answer elements
        for (var m = 0; m < answersTwo.length; m++) {
            answersTwo[m].classList.remove('show');
        }
        
        // Toggle the 'active' class for the corresponding answer element
        myanswer.classList.toggle('show');
    });
}