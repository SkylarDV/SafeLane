let canvas;
let game = "prior";
let bottomVehicleImg;
let rightVehicleImg;
let roadbg;
let bottomVehicle;
let rightVehicle;
let leftVehicle;
let topVehicle;

function preload() {
    roadbg = loadImage('../images/roadbg.png');
    bottomVehicleImg = loadImage('../images/bus-down.png');
    rightVehicleImg = loadImage('../images/bus-right.png');
    leftVehicleImg = loadImage('../images/bus-left.png');
    topVehicleImg = loadImage('../images/bus-up.png');
}

function setup() {
    canvas = createCanvas(windowWidth / 3, windowWidth / 3);
    positionCanvas();
    background(0);

    // Create instances of Vehicle
    bottomVehicle = new Vehicle(bottomVehicleImg, px(52), px(70), px(10), px(30));
    rightVehicle = new Vehicle(rightVehicleImg, px(70), px(35), px(30), px(10));
    leftVehicle = new Vehicle(leftVehicleImg, px(0), px(50), px(30), px(10));
    topVehicle = new Vehicle(topVehicleImg, px(38), px(0), px(10), px(30));
}

function windowResized() {
    resizeCanvas(windowWidth / 3, windowWidth / 3);
    positionCanvas();
    background(0);
}

function px(number) {
    if (windowWidth <= 650) {
        return number * (windowWidth) * 0.8 / 100;
    } else {
        return number * (windowWidth / 3) / 100;
    }
}

function positionCanvas() {
    if (windowWidth <= 650) {
        resizeCanvas(windowWidth * 0.8, windowWidth * 0.8);
        const xPos = (windowWidth - width) / 2;
        const yPos = (windowHeight - height) / 2;
        canvas.position(xPos, yPos);
    } else {
        const xPos = windowWidth / 2 + (windowWidth / 4) - width / 2;
        const yPos = (windowHeight / 2) - height / 2;
        canvas.position(xPos, yPos);
    }
}

class Vehicle {
    constructor(image, x, y, width, height) {
        this.image = image;
        this.x = x;
        this.y = y;
        this.width = width;
        this.height = height;
        this.isDragging = false;
    }

    mousePressed() {
        if (
            mouseX > this.x &&
            mouseX < this.x + this.width &&
            mouseY > this.y &&
            mouseY < this.y + this.height
        ) {
            this.isDragging = true;
        }
    }

    mouseReleased() {
        this.isDragging = false;
    }

    drag() {
        if (this.isDragging) {
            this.x = mouseX - this.width / 2;
            this.y = mouseY - this.height / 2;
        }
    }

    draw() {
        image(this.image, this.x, this.y, this.width, this.height);
    }
}

function mousePressed() {
    bottomVehicle.mousePressed();
    rightVehicle.mousePressed();
    leftVehicle.mousePressed();
    topVehicle.mousePressed();
}

function mouseReleased() {
    bottomVehicle.mouseReleased();
    rightVehicle.mouseReleased();
    leftVehicle.mouseReleased();
    topVehicle.mouseReleased();
}

function priorGame() {
    bottomVehicle.drag();
    rightVehicle.drag();
    leftVehicle.drag();
    topVehicle.drag();
    bottomVehicle.draw();
    rightVehicle.draw();
    leftVehicle.draw();
    //topVehicle.draw();
}

function draw() {
    clear();
    background(roadbg);
    if (game == "prior") {
        priorGame();
    }
}
