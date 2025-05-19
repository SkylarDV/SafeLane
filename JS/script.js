let canvas;
let game = "park";
let roadbg;
let priorGameInitialized = false;
let placementOrder = [];
let feedbackLight = null;
let itemSpots = [];
let items = [];
let objectGameInitialized = false;
let growingSign;
let slider;
let sliderValue = 10;
let parkTargets = [];

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

    slider = createSlider(10, 100, 10, 10);
    slider.style('width', px(40) + 'px');
    slider.position(
        width / 2 - px(20) + canvas.position().x,
        height / 2 + px(25) + canvas.position().y
    );
    slider.hide();

    let games = [
        { label: "Prioriteit", value: "prior" },
        { label: "Objecten", value: "object" },
        { label: "Verkeersbord", value: "sign" },
        { label: "Parkeren", value: "park" }
    ];
    let btnX = canvas.position().x;
    let btnY = canvas.position().y + height + 20; 
    for (let i = 0; i < games.length; i++) {
        let btn = createButton(games[i].label);
        btn.position(btnX + i * 110, btnY); 
        btn.mousePressed(() => {
            game = games[i].value;
        });
    }
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
            if (!this.inTargetZone) { 
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
            if (placementOrder[i] !== i + 1) { 
                feedbackLight = 'red';
                return;
            }
        }
        if (placementOrder.length === 4) { 
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
    constructor(x, y, width, height) {
        this.x = x;
        this.y = y;
        this.width = width;
        this.height = height;
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
        fill(0, 255, 0, 50); 
        rect(this.x, this.y, this.width, this.height);
    }
}

class Item {
    constructor(image, assignedSpot, necessary) {
        this.image = image;
        this.assignedSpot = assignedSpot; 
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

class GrowingSign {
    constructor(image, allowedSpeed) {
        this.image = image;
        this.size = px(20);
        this.allowedSpeed = allowedSpeed; 
        this.startTime = null;
        this.maxSize = px(40);
        this.minSize = px(20);
        this.duration = (this.maxSize - this.minSize) / this.allowedSpeed; 
    }

    update() {
        if (this.startTime === null) {
            this.startTime = millis();
        }
        let elapsed = (millis() - this.startTime) / 1000;
        let t = constrain(elapsed / this.duration, 0, 1);
        this.size = lerp(this.minSize, this.maxSize, t);
    }

    draw() {
        this.update();
        let x = width / 2 - this.size / 2;
        let y = height / 2 - this.size / 2;
        image(this.image, x, y, this.size, this.size);
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
    if (game === "park") {
        if (typeof parkVehicle !== "undefined") {
            parkVehicle.mousePressed();
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
        if (checkItemsRemovedCorrectly()) {
            feedbackLight = 'green';
        } else {
            feedbackLight = 'red';
        }
    }
    if (game === "park") {
        if (typeof parkVehicle !== "undefined") {
            parkVehicle.mouseReleased();

            
            let onTarget = false;
            for (let i = 0; i < parkTargets.length; i++) {
                let t = parkTargets[i];
                if (
                    parkVehicle.x + parkVehicle.width > t.x &&
                    parkVehicle.x < t.x + t.width &&
                    parkVehicle.y + parkVehicle.height > t.y &&
                    parkVehicle.y < t.y + t.height
                ) {
                    onTarget = true;
                    if (i === 4) { 
                        feedbackLight = 'green';
                    } else {
                        feedbackLight = 'red';
                    }
                    break;
                }
            }
            if (!onTarget) {
                feedbackLight = null;
            }
        }
    }
}

function draw() {
    clear();
    if (game == "prior") {
        background(roadbg);
        priorGame();
        if (slider) slider.hide(); 
    }
    if (game == "object") {
        objectGame();
        if (slider) slider.hide();
    }
    if (game == "sign") {
        signGame();
        if (slider) slider.show();
    }
    if (game == "park") {
        if (typeof parkVehicle !== "undefined") {
            parkVehicle.drag();
        }
        parkGame();
        if (slider) slider.hide(); 
    }
}

function priorGame() {
    if (!priorGameInitialized) {

        targetZoneTop = new Target(px(35), px(0), px(30), px(30));
        targetZoneBottom = new Target(px(35), px(70), px(30), px(30));
        targetZoneLeft = new Target(px(0), px(35), px(30), px(30));
        targetZoneRight = new Target(px(70), px(35), px(30), px(30));
        
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
    for (let item of items) {
        if (item.necessary && item.removed) {
            return false;
        }
        if (!item.necessary && !item.removed) {
            return false;
        }
    }
    return true;
}

function signGame() {
    if (!growingSign) {
        growingSign = new GrowingSign(bottomSignImg, 20);
    }

    growingSign.update();
    let signY = height / 2 - px(18) - growingSign.size / 2;
    let signX = width / 2 - growingSign.size / 2;
    image(growingSign.image, signX, signY, growingSign.size, growingSign.size);

    sliderValue = slider.value();

    textAlign(CENTER, CENTER);
    textSize(px(5));
    fill(0); 
    text(sliderValue, width / 2, height / 2 + px(28)); 

    slider.position(
        width / 2 - px(20) + canvas.position().x,
        height / 2 + px(33) + canvas.position().y 
    );

    if (growingSign.size >= growingSign.maxSize - 0.1) {
        slider.attribute('disabled', '');
        if (sliderValue == int(growingSign.allowedSpeed)) {
            feedbackLight = 'green';
        } else {
            feedbackLight = 'red';
        }
        console.log(feedbackLight)
    } else {
        slider.removeAttribute('disabled');
        feedbackLight = null;
    }

    slider.show();
}

function parkGame() {
    if (parkTargets.length === 0) {
        parkTargets.length = 0; 

        
        let marginX = px(8);
        let targetW = px(20);
        let targetH = px(30);
        let totalTargets = 3;
        let totalHeight = totalTargets * targetH;
        let spacingY = (height - totalHeight) / (totalTargets - 1);

        
        for (let i = 0; i < totalTargets; i++) {
            let y = i * (targetH + spacingY);
            parkTargets.push(new Target(marginX, y, targetW, targetH));
        }
        
        for (let i = 0; i < totalTargets; i++) {
            let y = i * (targetH + spacingY);
            parkTargets.push(new Target(width - targetW - marginX, y, targetW, targetH));
        }

        
        let vehicleW = px(20);
        let vehicleH = px(30);
        let vehicleX = width / 2 - vehicleW / 2;
        let vehicleY = height - vehicleH - px(5); 
       
        parkVehicle = new Vehicle(
            bottomVehicleImg,
            vehicleX,
            vehicleY,
            vehicleW,
            vehicleH,
            parkTargets[4], 
            1 
        );
    }

    for (let t of parkTargets) {
        fill(150);
        noStroke();
        rect(t.x, t.y, t.width, t.height);
    }

    if (typeof parkVehicle !== "undefined") {
        parkVehicle.drag();
        parkVehicle.draw();

        let onTarget = false;
        for (let i = 0; i < parkTargets.length; i++) {
            let t = parkTargets[i];
            if (
                parkVehicle.x + parkVehicle.width > t.x &&
                parkVehicle.x < t.x + t.width &&
                parkVehicle.y + parkVehicle.height > t.y &&
                parkVehicle.y < t.y + t.height
            ) {
                onTarget = true;
                if (i === 4) { 
                    feedbackLight = 'green';
                } else {
                    feedbackLight = 'red';
                }
                console.log(feedbackLight);
                break;
            }
        }
        if (!onTarget) {
            feedbackLight = null;
        }
    }
}