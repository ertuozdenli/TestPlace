var canvas = document.querySelector('canvas');

var genislik = window.innerWidth;
var yukseklik = window.innerHeight;

canvas.width = genislik;
canvas.height = yukseklik;

var c = canvas.getContext('2d');

class yuvarlak {
    yciz(x, y, w, h, fstyle = "#1a1a1ath") {
        this.x = x;
        this.y = y;
        this.w = w;
        this.h = h;
        this.fstyle = fstyle;
        c.beginPath();
        c.fillStyle = this.fstyle;
        c.arc(this.x, this.y, this.w, this.h, 1, 1);
        c.fill();
    }
}




var ortax = genislik / 2;
var ortah = yukseklik / 2;
var hiz = 5;
var yercekimi = 0.85;
// var randx = Math.random() * genislik;
// var randy = Math.random() * yukseklik;
var kullanicix = ortax,
    kullaniciy = ortah;

window.addEventListener("click", function (e) {
    kullanicix = e.clientX;
    kullaniciy = e.clientY;
    // nesne.yciz(kullanicix, kullaniciy, 50, 50);
});

var nesne = new yuvarlak();

function animate() {
    requestAnimationFrame(animate);

    c.clearRect(0, 0, genislik, yukseklik);

    nesne.yciz(kullanicix, kullaniciy, 50, 50);


    if (kullaniciy + 50 > yukseklik) {
        hiz = -hiz * yercekimi;
    } else {
        hiz += 1;
    }

    kullaniciy += hiz;

}

animate();