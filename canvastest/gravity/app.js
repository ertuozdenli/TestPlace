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
var yercekimi = 0.90;
// var randx = Math.random() * genislik;
// var randy = Math.random() * yukseklik;
var kullanicix = ortax,
    kullaniciy = ortah;

    var konum = [[ortax,ortah]];
    var hiz = [5];
    var adet = 1;
window.addEventListener("click", function (e) {
    kullanicix = e.clientX;
    kullaniciy = e.clientY;
    
    
    nesne.yciz(kullanicix, kullaniciy, 50, 50);
    konum[adet] = [kullanicix, kullaniciy];
    adet++;
    hiz.push(5);
});

var nesne = new yuvarlak();



function animate() {
    requestAnimationFrame(animate);

    c.clearRect(0, 0, genislik, yukseklik);

    // nesne.yciz(kullanicix, kullaniciy, 50, 50);
    for (var i = 0; i < adet; i++) {
        
        nesne.yciz(konum[i][0], konum[i][1], 50, 50);
        
        if (konum[i][1] + 50 > yukseklik) {
            hiz[i] = -hiz[i] * yercekimi;
        } else {
            hiz[i] += 1;
        }
    
        konum[i][1] += hiz[i];
    }

    
   

    

}

animate();