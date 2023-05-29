<script setup>
import { onMounted, watch } from "vue";
import gsap from "gsap"
import { useXmasStore } from "../../stores/xmas";
import { storeToRefs } from "pinia";
import PaintShape from "./PaintShape.vue";

const props = defineProps({
      color: String
})

const store = useXmasStore();
const { selectedColor } = storeToRefs(store);
const amount = 10;

const animate = () => {
      const paints = gsap.utils.toArray('.stage .paint');
      gsap.set(paints, { transformOrigin: 'center', left: 'random(0,100)%', top: 'random(0,80)%', xPercent: -50, yPercent: -50, rotate: "random(-180, 180)" });
      gsap.fromTo(paints, { opacity: 0, scale: 0.5 }, { opacity: 1, scale: "random(0.5, 0.9)", duration: 0.05, stagger: { amount: 1.7 }, delay: 0.3 });
}

watch(selectedColor, () => {
      animate();
})

onMounted(() => {
      animate();
})
</script>

<template>
      <PaintShape v-for="n in amount"
                  :key="n"
                  :color="color"
                  style="opacity: 0;" />
</template>
  