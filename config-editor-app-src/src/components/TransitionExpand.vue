<script setup>
// https://markus.oberlehner.net/blog/transition-to-height-auto-with-vue/

const enter = (element) => {
    const width = getComputedStyle(element).width;

    element.style.width = width;
    element.style.position = 'absolute';
    element.style.visibility = 'hidden';
    element.style.height = 'auto';

    const height = getComputedStyle(element).height;

    element.style.width = null;
    element.style.position = null;
    element.style.visibility = null;
    element.style.height = 0;

    // Force repaint to make sure the
    // animation is triggered correctly.
    getComputedStyle(element).height;

    // Trigger the animation.
    // We use `requestAnimationFrame` because we need
    // to make sure the browser has finished
    // painting after setting the `height`
    // to `0` in the line above.
    requestAnimationFrame(() => {
        element.style.height = height;
    });
}

const afterEnter = (element) => {
    element.style.height = 'auto';
}

const leave = (element) => {
    const height = getComputedStyle(element).height;

    element.style.height = height;

    // Force repaint to make sure the
    // animation is triggered correctly.
    getComputedStyle(element).height;

    requestAnimationFrame(() => {
        element.style.height = 0;
    });
}
</script>

<template>
    <transition name="expand"
                @enter="enter"
                @after-enter="afterEnter"
                @leave="leave">
        <slot />
    </transition>
</template>

<style scoped>
* {
    will-change: height;
    transform: translateZ(0);
    backface-visibility: hidden;
    perspective: 1000px;
}

.expand-enter-active,
.expand-leave-active {
    transition: height 0.3s ease-in-out;
    overflow: hidden;
}

.expand-enter,
.expand-leave-to {
    height: 0;
}
</style>