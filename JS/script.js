let canvas;
let game = "object";
let roadbg;
let priorGameInitialized = false;
let placementOrder = [];
let feedbackLight = null;
let itemSpots = [];
let items = [];
let objectGameInitialized = false;

function preload() {
    roadbg = loadImage('../images/roadbg.png');
    bottomVehicleImg = loadImage('../images/bus-down.png');
    rightVehicleImg = loadImage('../images/bus-right.png');
    leftVehicleImg = loadImage('../images/bus-left.png');
    topVehicleImg = loadImage('../images/bus-up.png');
    topSignImg = loadImage('../images/voorrangsweg-top.png');
    bottomSignImg = loadImage('../images/voorrangsweg-bottom.png');
    leftSignImg = loadImage('../images/voorrang-geven-left.png');
    rightSignImg = loadImage('../images/voorrang-geven-right.png');
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
        const xPos = windowWidth / 4 - width / 2;
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
        this.priority = priority;
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
            if (!this.inTargetZone) { // Only add to placementOrder if newly placed
                placementOrder.push(this.priority);
                this.checkPlacementOrder();
            }
            this.inTargetZone = true;
        } else {
            this.inTargetZone = false;
        }
    }

    checkPlacementOrder() {
        for (let i = 0; i < placementOrder.length; i++) {
            if (placementOrder[i] !== i + 1) { // Priorities should be in ascending order
                feedbackLight = 'red';
                return;
            }
        }
        if (placementOrder.length === 4) { // All vehicles placed correctly
            feedbackLight = 'green';
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

class TrafficSign {
    constructor(image, x, y) {
        this.image = image;
        this.x = x;
        this.y = y;
        this.width = px(10);
        this.height = px(10);
    }

    draw() {
        image(this.image, this.x, this.y, this.width, this.height);
    }
}
class ItemSpot {
    constructor(x, y) {
        this.x = x;
        this.y = y;
        this.width = px(20);
        this.height = px(30);
    }

    draw() {
        noStroke();
        fill(0, 255, 0, 50); // Light green, semi-transparent
        rect(this.x, this.y, this.width, this.height);
    }
}

class Item {
    constructor(image, assignedSpot, necessary) {
        this.image = image;
        this.assignedSpot = assignedSpot; // Instance of ItemSpot
        this.necessary = necessary;
        this.width = px(20);
        this.height = px(30);
        this.x = assignedSpot.x;
        this.y = assignedSpot.y;
        this.isDragging = false;
        this.removed = false;
    }

    mousePressed() {
        if (
            !this.removed &&
            mouseX > this.x &&
            mouseX < this.x + this.width &&
            mouseY > this.y &&
            mouseY < this.y + this.height
        ) {
            this.isDragging = true;
        }
    }

    mouseReleased() {
        if (this.isDragging) {
            this.isDragging = false;
            // If dropped in top half, remove
            if (this.y < height / 2) {
                this.removed = true;
            }
        }
    }

    drag() {
        if (this.isDragging && !this.removed) {
            this.x = mouseX - this.width / 2;
            this.y = mouseY - this.height / 2;
        }
    }

    draw() {
        if (!this.removed) {
            image(this.image, this.x, this.y, this.width, this.height);
        }
    }
}

function mousePressed() {
    if (game === "prior") {
        bottomVehicle.mousePressed();
        rightVehicle.mousePressed();
        leftVehicle.mousePressed();
        topVehicle.mousePressed();
    }
    if (game === "object") {
        for (let item of items) {
            item.mousePressed();
        }
    }
}

function mouseReleased() {
    if (game === "prior") {
        bottomVehicle.mouseReleased();
        rightVehicle.mouseReleased();
        leftVehicle.mouseReleased();
        topVehicle.mouseReleased();
    }
    if (game === "object") {
        for (let item of items) {
            item.mouseReleased();
        }
        // Check after releasing any item
        if (checkItemsRemovedCorrectly()) {
            feedbackLight = 'green';
        } else {
            feedbackLight = 'red';
        }
    }
}

function draw() {
    clear();
    if (game == "prior") {
        background(roadbg);
        priorGame();
        // Draw feedback light
        if (feedbackLight === 'green') {
            fill(0, 255, 0);
        } else if (feedbackLight === 'red') {
            fill(255, 0, 0);
        } else {
            fill(200); // Neutral light
        }
        ellipse(width - px(10), px(10), px(5), px(5)); // Light position
    }
    if (game == "object") {
        objectGame();
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

        sign1 = new TrafficSign(topSignImg, px(24), px(23));
        sign2 = new TrafficSign(leftSignImg, px(24), px(63));
        sign3 = new TrafficSign(bottomSignImg, px(67), px(63));
        sign4 = new TrafficSign(rightSignImg, px(67), px(23));
        
        priorGameInitialized = true;
    }
    if (priorGameInitialized) {
        //targetZoneTop.draw();
        //targetZoneBottom.draw();
        //targetZoneLeft.draw();
        //targetZoneRight.draw();

        bottomVehicle.drag();
        rightVehicle.drag();
        leftVehicle.drag();
        topVehicle.drag();

        bottomVehicle.draw();
        rightVehicle.draw();
        leftVehicle.draw();
        topVehicle.draw();
        
        sign1.draw();
        sign2.draw();
        sign3.draw();
        sign4.draw();
    }
}

function objectGame() {
    if (!objectGameInitialized) {
        // Calculate spacing
        let spacing = (width - 4 * px(20)) / 5;
        let y = height - px(30) - px(7);
        itemSpots = [];
        for (let i = 0; i < 4; i++) {
            let x = spacing + i * (px(20) + spacing);
            itemSpots.push(new ItemSpot(x, y));
        }
        items = [
            new Item(bottomVehicleImg, itemSpots[0], true),
            new Item(rightVehicleImg, itemSpots[1], false),
            new Item(leftVehicleImg, itemSpots[2], false),
            new Item(topVehicleImg, itemSpots[3], true)
        ];
        objectGameInitialized = true;
    }

    for (let y = 0; y < height; y++) {
        let inter = map(y, 0, height, 0, 1);
        let c = lerpColor(color('#1E3C51'), color('#192C3A'), inter);
        stroke(c);
        line(0, y, width, y);
    }
    for (let spot of itemSpots) {
        spot.draw();
    }
    for (let item of items) {
        item.drag();
        item.draw();
    }
}

function checkItemsRemovedCorrectly() {
    // Returns true if all unnecessary items are removed and all necessary items are not removed
    for (let item of items) {
        if (item.necessary && item.removed) {
            // A necessary item was removed (incorrect)
            return false;
        }
        if (!item.necessary && !item.removed) {
            // An unnecessary item was not removed (incorrect)
            return false;
        }
    }
    return true;
}
