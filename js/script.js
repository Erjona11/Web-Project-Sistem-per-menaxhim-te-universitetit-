const slides = document.querySelectorAll(".slide");

console.log("JS u ngarkua");
console.log("Slides:", slides.length);

const photos = [
    "images/foto1.png",
    "images/foto2.png",
    "images/foto3.png",
    "images/foto4.png",
    "images/foto5.png",
    "images/foto6.png"
];

let currentPhoto = 0;

slides[0].style.backgroundImage = `url("${photos[0]}")`;

setInterval(function () {

    currentPhoto++;

    if (currentPhoto >= photos.length) {
        currentPhoto = 0;
    }

    slides[0].style.backgroundImage =
        `url("${photos[currentPhoto]}")`;

}, 3000);