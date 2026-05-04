new Vue({
    el: '#app',
    data: {
        currentSlide: 0,
        properties: typeof propertiesData !== 'undefined' ? propertiesData : [],
        autoplayInterval: null
    },
    mounted() {
        this.startAutoplay();
    },
    beforeDestroy() {
        this.stopAutoplay();
    },
    methods: {
        nextSlide() {
            this.currentSlide = (this.currentSlide === this.properties.length - 1) ? 0 : this.currentSlide + 1;
        },
        prevSlide() {
            this.currentSlide = (this.currentSlide === 0) ? this.properties.length - 1 : this.currentSlide - 1;
        },
        goToSlide(index) {
            this.currentSlide = index;
            this.restartAutoplay();
        },
        startAutoplay() {
            this.autoplayInterval = setInterval(() => {
                this.nextSlide();
            }, 4000);
        },
        stopAutoplay() {
            clearInterval(this.autoplayInterval);
        },
        restartAutoplay() {
            this.stopAutoplay();
            this.startAutoplay();
        }
    }
});