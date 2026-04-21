Vue.component('progress-bar', {
    props: ['progress', 'status', 'time'],
    data() {
        return {
            filledProgress: 0
        };
    },
    mounted() {
        this.animateProgress();
    },
    methods: {
        animateProgress() {
            const duration = 1000;
            const start = performance.now();
            const animate = (time) => {
                const elapsed = time - start;
                const progress = Math.min(elapsed / duration, 1);
                this.filledProgress = this.progress * progress;
                if (progress < 1) {
                    requestAnimationFrame(animate);
                }
            };
            requestAnimationFrame(animate);
        },
        resetAnimation() {
            this.filledProgress = 0;
            this.animateProgress();
        }
    },
    watch: {
        filledProgress() {
            if (this.filledProgress === 0) {
                this.animateProgress();
            }
        }
    },
    template: `
        <div class="progress-bar-container">
            <div class="progress-bar-header">
                <span class="progress-percentage">{{ progress }}%</span>
                <span class="progress-status">{{ status }}</span>
                <span class="progress-time">{{ time }}</span>
            </div>
            <div class="progress-bar">
                <div class="progress-bar-fill" :style="{ width: filledProgress + '%' }"></div>
            </div>
        </div>
    `
});

new Vue({
    el: '#app',
    data: {
        currentSlide: 0,
        properties: [
            {
                title: 'Silver Height',
                price: '$420,000',
                description: 'Просторный дом с видом на море, который включает 4 спальни и 3 ванные комнаты.',
                image: '/images/example-house.jpg',
                progress: 75,
                status: 'В процессе',
                time: '1 год до завершения',
                icons: [
                    { src: '/images/bed.svg', alt: 'Спальни', label: '4 спальни' },
                    { src: '/images/bath.svg', alt: 'Ванные', label: '3 ванные' },
                    { src: '/images/square.svg', alt: 'Гараж', label: '350м²' }
                ]
            },
            {
                title: 'Rose Height',
                price: '$320,000',
                description: 'Просторный дом с видом на море, который включает 4 спальни и 3 ванные комнаты.',
                image: '/images/example-house3.jpg',
                progress: 25,
                status: 'В процессе',
                time: '3 год до завершения',
                icons: [
                    { src: '/images/bed.svg', alt: 'Спальни', label: '4 спальни' },
                    { src: '/images/bath.svg', alt: 'Ванные', label: '3 ванные' },
                    { src: '/images/square.svg', alt: 'Гараж', label: '350м²' }
                ]
            },
            {
                title: 'Atlanta House',
                price: '$120,000',
                description: 'Просторный дом с видом на море, который включает 4 спальни и 3 ванные комнаты.',
                image: '/images/example-house2.jpg',
                progress: 50,
                status: 'В процессе',
                time: '2 года до завершения',
                icons: [
                    { src: '/images/bed.svg', alt: 'Спальни', label: '4 спальни' },
                    { src: '/images/bath.svg', alt: 'Ванные', label: '3 ванные' },
                    { src: '/images/square.svg', alt: 'Гараж', label: '350м²' }
                ]
            }
        ]
    },
    methods: {
        nextSlide() {
            if (this.currentSlide < this.properties.length - 1) {
                this.currentSlide++;
            } else {
                this.currentSlide = 0;
            }
        },
        prevSlide() {
            if (this.currentSlide > 0) {
                this.currentSlide--;
            } else {
                this.currentSlide = this.properties.length - 1;
            }
        }
    },
    watch: {
        currentSlide() {
            this.$nextTick(() => {
                const progressBar = this.$refs.progressBar;
                if (progressBar && progressBar[this.currentSlide]) {
                    progressBar[this.currentSlide].resetAnimation();
                }
            });
        }
    }
});
