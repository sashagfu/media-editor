let cb440x1,
    cb440x2,
    cb440x3,
    cb440x4,
    cb440x5,
    cb440x6,
    cb440x7,
    cb440x8 = true;

function cb440x26(cb440x11, cb440x13) {
    return Math.pow(cb440x11.x - cb440x13.x, 2) + Math.pow(cb440x11.y - cb440x13.y, 2);
}

function cb440x9() {
    cb440x1 = window.innerWidth;
    cb440x2 = window.innerHeight;
    cb440x7 = {
        x: cb440x1 / 2,
        y: cb440x2 / 2,
    };
    cb440x3 = document.getElementById('top');
    cb440x3.style.height = `${cb440x2}px`;
    cb440x4 = document.getElementById('constellation');
    cb440x4.width = cb440x1;
    cb440x4.height = cb440x2;
    cb440x5 = cb440x4.getContext('2d');
    cb440x6 = [];
    for (let cb440xa = 0; cb440xa < cb440x1; cb440xa += cb440x1 / 20) {
        for (let cb440xb = 0; cb440xb < cb440x2; cb440xb += cb440x2 / 20) {
            const cb440xc = cb440xa + Math.random() * cb440x1 / 20;
            const cb440xd = cb440xb + Math.random() * cb440x2 / 20;
            const cb440xe = {
                x: cb440xc,
                originX: cb440xc,
                y: cb440xd,
                originY: cb440xd,
            };
            cb440x6.push(cb440xe);
        }
    }
    for (var cb440xf = 0; cb440xf < cb440x6.length; cb440xf++) {
        const cb440x10 = [];
        const cb440x11 = cb440x6[cb440xf];
        for (let cb440x12 = 0; cb440x12 < cb440x6.length; cb440x12++) {
            const cb440x13 = cb440x6[cb440x12];
            if (!(cb440x11 == cb440x13)) {
                let cb440x14 = false;
                for (var cb440x15 = 0; cb440x15 < 5; cb440x15++) {
                    if (!cb440x14) {
                        if (cb440x10[cb440x15] == undefined) {
                            cb440x10[cb440x15] = cb440x13;
                            cb440x14 = true;
                        }
                    }
                }
                for (var cb440x15 = 0; cb440x15 < 5; cb440x15++) {
                    if (!cb440x14) {
                        if (cb440x26(cb440x11, cb440x13) < cb440x26(cb440x11, cb440x10[cb440x15])) {
                            cb440x10[cb440x15] = cb440x13;
                            cb440x14 = true;
                        }
                    }
                }
            }
        }
        cb440x11.closest = cb440x10;
    }
    for (var cb440xf in cb440x6) {
        const cb440x16 = new cb440x21(cb440x6[cb440xf], 2 + Math.random() * 2, 'rgba(64,125,61,0.3)');
        cb440x6[cb440xf].circle = cb440x16;
    }
}

function cb440x17() {
    if (!('ontouchstart' in window)) {
        window.addEventListener('mousemove', cb440x18);
    }
    window.addEventListener('scroll', cb440x1b);
    window.addEventListener('resize', cb440x1c);
}

function cb440x18(cb440x19) {
    let posy = 0;
    let cb440x1a = 0;
    if (cb440x19.pageX || cb440x19.pageY) {
        cb440x1a = cb440x19.pageX;
        posy = cb440x19.pageY;
    } else if (cb440x19.clientX || cb440x19.clientY) {
        cb440x1a = cb440x19.clientX
            + document.body.scrollLeft
            + document.documentElement.scrollLeft;
        posy = cb440x19.clientY + document.body.scrollTop + document.documentElement.scrollTop;
    }
    cb440x7.x = cb440x1a;
    cb440x7.y = posy;
}

function cb440x1b() {
    if (document.body.scrollTop > cb440x2) {
        cb440x8 = false;
    } else {
        cb440x8 = true;
    }
}

function cb440x1c() {
    cb440x1 = window.innerWidth;
    cb440x2 = window.innerHeight;
    cb440x3.style.height = `${cb440x2}px`;
    cb440x4.width = cb440x1;
    cb440x4.height = cb440x2;
}

function cb440x1d() {
    cb440x1e();
    for (const cb440xf in cb440x6) {
        cb440x1f(cb440x6[cb440xf]);
    }
}

function cb440x1e() {
    if (cb440x8) {
        cb440x5.clearRect(0, 0, cb440x1, cb440x2);
        for (const cb440xf in cb440x6) {
            if (Math.abs(cb440x26(cb440x7, cb440x6[cb440xf])) < 4000) {
                cb440x6[cb440xf].active = 0.3;
                cb440x6[cb440xf].circle.active = 0.6;
            } else if (Math.abs(cb440x26(cb440x7, cb440x6[cb440xf])) < 20000) {
                cb440x6[cb440xf].active = 0.1;
                cb440x6[cb440xf].circle.active = 0.3;
            } else if (Math.abs(cb440x26(cb440x7, cb440x6[cb440xf])) < 40000) {
                cb440x6[cb440xf].active = 0.02;
                cb440x6[cb440xf].circle.active = 0.1;
            } else {
                cb440x6[cb440xf].active = 0;
                cb440x6[cb440xf].circle.active = 0;
            }
            cb440x20(cb440x6[cb440xf]);
            cb440x6[cb440xf].circle.draw();
        }
    }
    requestAnimationFrame(cb440x1e);
}

function cb440x1f(cb440xe) {
    TweenLite.to(cb440xe, 1 + 1 * Math.random(), {
        x: cb440xe.originX - 50 + Math.random() * 100,
        y: cb440xe.originY - 50 + Math.random() * 100,
        ease: Circ.easeInOut,
        onComplete() {
            cb440x1f(cb440xe);
        },
    });
}

function cb440x20(cb440xe) {
    if (!cb440xe.active) {
        return;
    }
    for (const cb440xf in cb440xe.closest) {
        cb440x5.beginPath();
        cb440x5.moveTo(cb440xe.x, cb440xe.y);
        cb440x5.lineTo(cb440xe.closest[cb440xf].x, cb440xe.closest[cb440xf].y);
        cb440x5.strokeStyle = `rgba(64,125,61,${cb440xe.active})`;
        cb440x5.stroke();
    }
}

function cb440x21(cb440x22, cb440x23, cb440x24) {
    const cb440x25 = this;
    (function() {
        cb440x25.pos = cb440x22 || null;
        cb440x25.radius = cb440x23 || null;
        cb440x25.color = cb440x24 || null;
    }());
    this.draw = function() {
        if (!cb440x25.active) {
            return;
        }
        cb440x5.beginPath();
        cb440x5.arc(cb440x25.pos.x, cb440x25.pos.y, cb440x25.radius, 0, 2 * Math.PI, false);
        cb440x5.fillStyle = `rgba(64,125,61,${cb440x25.active})`;
        cb440x5.fill();
    };
}

cb440x9();
cb440x1d();
cb440x17();
