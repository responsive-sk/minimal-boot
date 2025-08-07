<template>
  <div class="fixed inset-0 pointer-events-none z-0">
    <div 
      v-for="column in matrixColumns" 
      :key="column.id"
      class="absolute top-0 matrix-char text-xs leading-tight"
      :style="{
        left: column.x + '%',
        animationDelay: column.delay + 's',
        animationDuration: column.duration + 's'
      }"
    >
      <div 
        v-for="(char, index) in column.chars" 
        :key="index"
        class="block opacity-80"
        :style="{ animationDelay: (index * 0.1) + 's' }"
      >
        {{ char }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const matrixColumns = ref([])

const matrixChars = '01アイウエオカキクケコサシスセソタチツテトナニヌネノハヒフヘホマミムメモヤユヨラリルレロワヲン'

function generateMatrixColumn(id) {
  const chars = []
  const charCount = Math.floor(Math.random() * 20) + 10
  
  for (let i = 0; i < charCount; i++) {
    chars.push(matrixChars[Math.floor(Math.random() * matrixChars.length)])
  }
  
  return {
    id,
    x: Math.random() * 100,
    chars,
    delay: Math.random() * 5,
    duration: 15 + Math.random() * 10
  }
}

onMounted(() => {
  // Generate matrix columns
  for (let i = 0; i < 30; i++) {
    matrixColumns.value.push(generateMatrixColumn(i))
  }
  
  // Regenerate columns periodically
  setInterval(() => {
    const randomIndex = Math.floor(Math.random() * matrixColumns.value.length)
    matrixColumns.value[randomIndex] = generateMatrixColumn(randomIndex)
  }, 3000)
})
</script>

<style scoped>
.matrix-char {
  animation: matrixRain linear infinite;
  font-family: 'Courier New', monospace;
  color: #00ff41;
  text-shadow: 0 0 5px currentColor;
}

@keyframes matrixRain {
  0% { 
    transform: translateY(-100vh);
    opacity: 0;
  }
  10% {
    opacity: 1;
  }
  90% {
    opacity: 1;
  }
  100% { 
    transform: translateY(100vh);
    opacity: 0;
  }
}
</style>
