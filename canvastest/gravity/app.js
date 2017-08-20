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

function getRandColor() {
    var renk = "rgb(";
    for (i = 1; i <= 3; i++) {
        renk += parseInt(Math.random() * 255);
        if (i < 3) {
            renk += ",";
        }
    }
    renk += ")";
    return renk;
}

var clicked = false;

var ortax = genislik / 2;
var ortah = yukseklik / 2;
var yercekimi = 0.85;
// var randx = Math.random() * genislik;
// var randy = Math.random() * yukseklik;
var kullanicix = ortax,
    kullaniciy = ortah;

var konum = [
    [ortax, ortah, getRandColor()]
];
var hiz = [3];
var adet = 1;

window.addEventListener("click", function (e) {
    kullanicix = e.clientX;
    kullaniciy = e.clientY;
    var renk = getRandColor();
    // console.log(renk);
    nesne.yciz(kullanicix, kullaniciy, 35, 35, renk);
    konum[adet] = [kullanicix, kullaniciy, renk];
    adet++;
    hiz.push(3);
});

window.addEventListener("mousedown", function () {
    clicked = true;
});

// window.addEventListener("mouseup", function () {
//     clicked = false;
// });

// window.addEventListener("mousemove",function(e){

//     if(clicked){
//         kullanicix = e.clientX;
//         kullaniciy = e.clientY;
//         var renk = getRandColor();
//         // console.log(renk);
//         nesne.yciz(kullanicix, kullaniciy, 35, 35,renk);
//         konum[adet] = [kullanicix, kullaniciy, renk];
//         adet++;
//         hiz.push(3);
//     }

// });

var nesne = new yuvarlak();

function animate() {
    requestAnimationFrame(animate);

    c.clearRect(0, 0, genislik, yukseklik);


    for (var i = 0; i < adet; i++) {

        nesne.yciz(konum[i][0], konum[i][1], 35, 35, konum[i][2]);

        if (konum[i][1] + 35 > yukseklik) {
            hiz[i] = -hiz[i] * yercekimi;
        } else {
            hiz[i] += 1;
        }

        konum[i][1] += hiz[i];
    }

}

animate();