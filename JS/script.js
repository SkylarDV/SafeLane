let canvas;
let game = "prior";
let bottomVehicleImg;
let rightVehicleImg;
let roadbg;
let bottomVehicle;
let rightVehicle;
let leftVehicle;
let topVehicle;
let priorGameInitialized = false;

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
    constructor(image, x, y, width, height, targetZone, priority) {
        this.image = image;
        this.x = x;
        this.y = y;
        this.width = width;
        this.height = height;
        this.isDragging = false;
        this.targetZone = targetZone;
        this.inTargetZone = false;
        this.priority = this.priority;
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
        this.checkInTargetZone();
    }

    drag() {
        if (this.isDragging) {
            this.x = mouseX - this.width / 2;
            this.y = mouseY - this.height / 2;
        }
    }

    checkInTargetZone() {
        if (
            this.x + this.width > this.targetZone.x &&
            this.x < this.targetZone.x + this.targetZone.width &&
            this.y + this.height > this.targetZone.y &&
            this.y < this.targetZone.y + this.targetZone.height
        ) {
            this.inTargetZone = true;
        } else {
            this.inTargetZone = false;
        }
    }

    draw() {
        if (!this.inTargetZone) {
            image(this.image, this.x, this.y, this.width, this.height);
        }
        
    }
}

class Target {
    constructor(x, y) {
        this.x = x;
        this.y = y;
        this.width = px(30);
        this.height = px(30);
    }

    draw() {
        noStroke();
        fill(255, 0, 0, 50)
        rect(this.x, this.y, px(30), px(30));
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

function draw() {
    clear();
    background(roadbg);
    if (game == "prior") {
        priorGame();
    }
}

function priorGame() {
    if (!priorGameInitialized) {
        // Create vehicles and target zones only once

        targetZoneTop = new Target(px(35), px(0));
        targetZoneBottom = new Target(px(35), px(70));
        targetZoneLeft = new Target(px(0), px(35));
        targetZoneRight = new Target(px(70), px(35));
        
        bottomVehicle = new Vehicle(bottomVehicleImg, px(52), px(70), px(10), px(30), targetZoneTop, 1);
        rightVehicle = new Vehicle(rightVehicleImg, px(70), px(35), px(30), px(10), targetZoneBottom, 3);
        leftVehicle = new Vehicle(leftVehicleImg, px(0), px(50), px(30), px(10), targetZoneRight, 2);
        topVehicle = new Vehicle(topVehicleImg, px(38), px(0), px(10), px(30), targetZoneLeft, 4);

        priorGameInitialized = true;
    }
    if (priorGameInitialized) {
        targetZoneTop.draw();
        targetZoneBottom.draw();
        targetZoneLeft.draw();
        targetZoneRight.draw();

        bottomVehicle.drag();
        rightVehicle.drag();
        leftVehicle.drag();
        //topVehicle.drag();

        bottomVehicle.draw();
        rightVehicle.draw();
        leftVehicle.draw();
        //topVehicle.draw();
    }

    
}


