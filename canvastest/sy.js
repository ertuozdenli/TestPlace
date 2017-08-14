var canvas = document.querySelector("canvas");
canvas.width = window.innerWidth;
canvas.height = window.innerHeight;

var eortax = window.innerWidth / 2;
var eortay = window.innerHeight / 2;

var c = canvas.getContext('2d');

function kareciz(x, y, w, h) {
  this.x = x;
  this.y = y;
  this.w = w;
  this.h = h;
  c.beginPath();
  c.rect(this.x, this.y, this.w, this.h);
  c.fillStyle = "#1a1a1a";
  c.fill();
}
var sag = false,
  sol = false,
  yuk = false,
  asa = false;

function sifirla() {
  sag = false, sol = false, yuk = false, asa = false;
}

var x, y, w, h;
x = (Math.random() * canvas.width) + 1;
y = (Math.random() * canvas.height) + 1;
w = (Math.random() * 100) + 10;
h = (Math.random() * 100) + 10;
// x=eortax;
// y=eortay+50;
w = 100;
h = 100;
var hiz = 3;
asa = true;

document.addEventListener("keydown", function(event) {
  console.log(event.keyCode);

  if (event.keyCode == 37) {
    sifirla();
    sol = true;
  }
  if (event.keyCode == 38) {
    sifirla();
    yuk = true;
  }
  if (event.keyCode == 39) {
    sifirla();
    sag = true;
  }
  if (event.keyCode == 40) {
    sifirla();
    asa = true;
  }

  if (event.keyCode == 86) {
    hiz++;
  }
  if (event.keyCode == 67) {
    if (hiz != 0) {
      hiz--;
    }
  }
  if (event.keyCode == 32) {
    hiz = 0;
  }
});

function animation() {
  requestAnimationFrame(animation);

  c.clearRect(0, 0, canvas.width, canvas.height);
  kareciz(x, y, w, h);

  if (y + 100 > canvas.height) {
    asa = false;
    yuk = true;
  }
  if (y + 100 < 100) {
    asa = true;
    yuk = false;
  }

  if (x + 100 > canvas.width) {
    sag = false;
    sol = true;
  }

  if (x + 100 < 100) {
    sag = true;
    sol = false;
  }

  if (sol) {
    x -= hiz;
  }
  if (sag) {
    x += hiz;
  }
  if (yuk) {
    y -= hiz;
  }
  if (asa) {
    y += hiz;
  }


  // x += hiz;

}

animation();
