/**
 * SANADK - Circular Charts Library
 * Dynamic circular progress charts with percentages
 */

class CircularChart {
    constructor(canvasId, options = {}) {
        this.canvas = document.getElementById(canvasId);
        this.ctx = this.canvas.getContext('2d');
        this.options = {
            size: 200,
            lineWidth: 15,
            percentage: 0,
            color: '#6FA8DC',
            backgroundColor: '#E8F1FB',
            textColor: '#2D3436',
            ...options
        };
        
        this.setup();
    }

    setup() {
        this.canvas.width = this.options.size;
        this.canvas.height = this.options.size;
        this.centerX = this.options.size / 2;
        this.centerY = this.options.size / 2;
        this.radius = (this.options.size - this.options.lineWidth) / 2;
    }

    draw() {
        this.ctx.clearRect(0, 0, this.options.size, this.options.size);
        
        // Draw background circle
        this.drawCircle(this.options.backgroundColor, 0, 360);
        
        // Draw progress circle
        const angle = (this.options.percentage / 100) * 360;
        this.drawCircle(this.options.color, 0, angle);
        
        // Draw percentage text
        this.drawText();
    }

    drawCircle(color, startAngle, endAngle) {
        this.ctx.beginPath();
        this.ctx.strokeStyle = color;
        this.ctx.lineWidth = this.options.lineWidth;
        this.ctx.lineCap = 'round';
        
        const startRad = (startAngle - 90) * Math.PI / 180;
        const endRad = (endAngle - 90) * Math.PI / 180;
        
        this.ctx.arc(
            this.centerX,
            this.centerY,
            this.radius,
            startRad,
            endRad
        );
        
        this.ctx.stroke();
    }

    drawText() {
        this.ctx.fillStyle = this.options.textColor;
        this.ctx.font = `bold ${this.options.size * 0.3}px 'Cairo', sans-serif`;
        this.ctx.textAlign = 'center';
        this.ctx.textBaseline = 'middle';
        
        const text = `${Math.round(this.options.percentage)}%`;
        this.ctx.fillText(text, this.centerX, this.centerY);
    }

    update(percentage) {
        this.options.percentage = Math.min(100, Math.max(0, percentage));
        this.draw();
    }

    setColor(color) {
        this.options.color = color;
        this.draw();
    }
}

/**
 * Multi-color Circular Chart
 * For displaying multiple metrics in one circle
 */
class MultiColorCircularChart {
    constructor(canvasId, options = {}) {
        this.canvas = document.getElementById(canvasId);
        this.ctx = this.canvas.getContext('2d');
        this.options = {
            size: 200,
            lineWidth: 15,
            segments: [],
            ...options
        };
        
        this.setup();
    }

    setup() {
        this.canvas.width = this.options.size;
        this.canvas.height = this.options.size;
        this.centerX = this.options.size / 2;
        this.centerY = this.options.size / 2;
        this.radius = (this.options.size - this.options.lineWidth) / 2;
    }

    draw() {
        this.ctx.clearRect(0, 0, this.options.size, this.options.size);
        
        let currentAngle = -90;
        const totalPercentage = this.options.segments.reduce((sum, seg) => sum + seg.percentage, 0);
        
        // Draw segments
        this.options.segments.forEach(segment => {
            const segmentAngle = (segment.percentage / totalPercentage) * 360;
            this.drawSegment(segment.color, currentAngle, segmentAngle);
            currentAngle += segmentAngle;
        });
        
        // Draw center circle (donut effect)
        this.drawCenterCircle();
    }

    drawSegment(color, startAngle, angle) {
        this.ctx.beginPath();
        this.ctx.strokeStyle = color;
        this.ctx.lineWidth = this.options.lineWidth;
        this.ctx.lineCap = 'round';
        
        const startRad = startAngle * Math.PI / 180;
        const endRad = (startAngle + angle) * Math.PI / 180;
        
        this.ctx.arc(
            this.centerX,
            this.centerY,
            this.radius,
            startRad,
            endRad
        );
        
        this.ctx.stroke();
    }

    drawCenterCircle() {
        this.ctx.beginPath();
        this.ctx.fillStyle = 'white';
        this.ctx.arc(this.centerX, this.centerY, this.radius - this.options.lineWidth - 5, 0, 2 * Math.PI);
        this.ctx.fill();
    }

    update(segments) {
        this.options.segments = segments;
        this.draw();
    }
}

/**
 * Gauge Chart
 * Semicircular gauge for showing ranges
 */
class GaugeChart {
    constructor(canvasId, options = {}) {
        this.canvas = document.getElementById(canvasId);
        this.ctx = this.canvas.getContext('2d');
        this.options = {
            size: 200,
            lineWidth: 15,
            percentage: 0,
            minColor: '#00B894',
            midColor: '#FDCB6E',
            maxColor: '#FF6B6B',
            textColor: '#2D3436',
            ...options
        };
        
        this.setup();
    }

    setup() {
        this.canvas.width = this.options.size;
        this.canvas.height = this.options.size / 2 + 40;
        this.centerX = this.options.size / 2;
        this.centerY = this.options.size / 2;
        this.radius = (this.options.size - this.options.lineWidth) / 2;
    }

    draw() {
        this.ctx.clearRect(0, 0, this.options.size, this.options.size / 2 + 40);
        
        // Draw background gauge
        this.drawGauge('#E8F1FB', 0, 180);
        
        // Determine color based on percentage
        let color = this.options.minColor;
        if (this.options.percentage > 66) {
            color = this.options.maxColor;
        } else if (this.options.percentage > 33) {
            color = this.options.midColor;
        }
        
        // Draw progress gauge
        const angle = (this.options.percentage / 100) * 180;
        this.drawGauge(color, 0, angle);
        
        // Draw needle
        this.drawNeedle(angle);
        
        // Draw text
        this.drawText();
    }

    drawGauge(color, startAngle, endAngle) {
        this.ctx.beginPath();
        this.ctx.strokeStyle = color;
        this.ctx.lineWidth = this.options.lineWidth;
        this.ctx.lineCap = 'round';
        
        const startRad = (startAngle) * Math.PI / 180;
        const endRad = (endAngle) * Math.PI / 180;
        
        this.ctx.arc(
            this.centerX,
            this.centerY,
            this.radius,
            startRad,
            endRad
        );
        
        this.ctx.stroke();
    }

    drawNeedle(angle) {
        const needleLength = this.radius - this.options.lineWidth;
        const needleAngle = angle * Math.PI / 180;
        
        const endX = this.centerX + needleLength * Math.cos(needleAngle);
        const endY = this.centerY + needleLength * Math.sin(needleAngle);
        
        this.ctx.beginPath();
        this.ctx.strokeStyle = '#2D3436';
        this.ctx.lineWidth = 2;
        this.ctx.moveTo(this.centerX, this.centerY);
        this.ctx.lineTo(endX, endY);
        this.ctx.stroke();
        
        // Draw center circle
        this.ctx.beginPath();
        this.ctx.fillStyle = '#2D3436';
        this.ctx.arc(this.centerX, this.centerY, 5, 0, 2 * Math.PI);
        this.ctx.fill();
    }

    drawText() {
        this.ctx.fillStyle = this.options.textColor;
        this.ctx.font = `bold 24px 'Cairo', sans-serif`;
        this.ctx.textAlign = 'center';
        this.ctx.textBaseline = 'top';
        
        const text = `${Math.round(this.options.percentage)}%`;
        this.ctx.fillText(text, this.centerX, this.centerY + 30);
    }

    update(percentage) {
        this.options.percentage = Math.min(100, Math.max(0, percentage));
        this.draw();
    }
}

/**
 * Donut Chart
 * For displaying data distribution
 */
class DonutChart {
    constructor(canvasId, options = {}) {
        this.canvas = document.getElementById(canvasId);
        this.ctx = this.canvas.getContext('2d');
        this.options = {
            size: 200,
            lineWidth: 20,
            data: [],
            ...options
        };
        
        this.setup();
    }

    setup() {
        this.canvas.width = this.options.size;
        this.canvas.height = this.options.size;
        this.centerX = this.options.size / 2;
        this.centerY = this.options.size / 2;
        this.radius = (this.options.size - this.options.lineWidth) / 2;
    }

    draw() {
        this.ctx.clearRect(0, 0, this.options.size, this.options.size);
        
        const total = this.options.data.reduce((sum, item) => sum + item.value, 0);
        let currentAngle = -Math.PI / 2;
        
        // Draw segments
        this.options.data.forEach(item => {
            const sliceAngle = (item.value / total) * 2 * Math.PI;
            this.drawSegment(item.color, currentAngle, sliceAngle);
            currentAngle += sliceAngle;
        });
        
        // Draw center circle (donut effect)
        this.drawCenterCircle();
    }

    drawSegment(color, startAngle, angle) {
        this.ctx.beginPath();
        this.ctx.fillStyle = color;
        this.ctx.moveTo(this.centerX, this.centerY);
        this.ctx.arc(
            this.centerX,
            this.centerY,
            this.radius,
            startAngle,
            startAngle + angle
        );
        this.ctx.lineTo(this.centerX, this.centerY);
        this.ctx.fill();
    }

    drawCenterCircle() {
        this.ctx.beginPath();
        this.ctx.fillStyle = 'white';
        this.ctx.arc(
            this.centerX,
            this.centerY,
            this.radius - this.options.lineWidth,
            0,
            2 * Math.PI
        );
        this.ctx.fill();
    }

    update(data) {
        this.options.data = data;
        this.draw();
    }
}

/**
 * Animated Circular Chart
 * Animates from 0 to target percentage
 */
class AnimatedCircularChart extends CircularChart {
    constructor(canvasId, options = {}) {
        super(canvasId, options);
        this.targetPercentage = 0;
        this.currentPercentage = 0;
        this.animationDuration = options.animationDuration || 1000;
        this.animationStartTime = null;
    }

    animateTo(percentage) {
        this.targetPercentage = percentage;
        this.animationStartTime = Date.now();
        this.animate();
    }

    animate() {
        const now = Date.now();
        const elapsed = now - this.animationStartTime;
        const progress = Math.min(elapsed / this.animationDuration, 1);
        
        this.currentPercentage = this.options.percentage + (this.targetPercentage - this.options.percentage) * progress;
        this.options.percentage = this.currentPercentage;
        this.draw();
        
        if (progress < 1) {
            requestAnimationFrame(() => this.animate());
        }
    }
}

// Export for use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        CircularChart,
        MultiColorCircularChart,
        GaugeChart,
        DonutChart,
        AnimatedCircularChart
    };
}
