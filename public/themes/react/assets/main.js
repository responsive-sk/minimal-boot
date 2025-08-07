// Forest Calm React Theme - Digital Nature Sanctuary
// Simple vanilla JS version for immediate testing

(function() {
  'use strict';

  // Forest Calm Theme Initialization
  function initForestCalm() {
    console.log('🌲 Forest Calm theme initializing - breathe deeply...');
    
    // Wait for React mount point
    const reactApp = document.getElementById('react-app');
    if (!reactApp) {
      console.log('🍃 Waiting for forest to grow...');
      setTimeout(initForestCalm, 50);
      return;
    }

    console.log('🌿 Forest ecosystem ready - welcome to digital nature');
    
    // Create simple React-like experience with vanilla JS
    createForestExperience(reactApp);
    
    // Initialize forest features
    initBreathingGuide();
    initFallingLeaves();
    initAmbientEffects();
    
    console.log('🧘‍♀️ Forest Calm fully loaded - find your peace');
  }

  // Create Forest Experience
  function createForestExperience(container) {
    // Add forest atmosphere
    container.innerHTML = `
      <div class="forest-atmosphere">
        <!-- Gentle breathing indicator -->
        <div class="breathing-guide fixed top-4 right-4 z-50">
          <div class="breathing-circle w-16 h-16 rounded-full bg-green-500/30 flex items-center justify-center text-2xl">
            🫁
          </div>
          <p class="text-green-200 text-sm mt-2 text-center">Breathe</p>
        </div>
        
        <!-- Floating leaves container -->
        <div class="falling-leaves-container fixed inset-0 pointer-events-none z-10"></div>
        
        <!-- Ambient light effects -->
        <div class="ambient-lights fixed inset-0 pointer-events-none">
          <div class="light-orb-1 absolute w-96 h-96 rounded-full opacity-20 blur-3xl"></div>
          <div class="light-orb-2 absolute w-64 h-64 rounded-full opacity-15 blur-2xl"></div>
        </div>
      </div>
    `;
  }

  // Breathing Guide
  function initBreathingGuide() {
    const breathingCircle = document.querySelector('.breathing-circle');
    const breathingText = document.querySelector('.breathing-guide p');
    
    if (!breathingCircle || !breathingText) return;

    let phase = 'inhale';
    const phases = {
      inhale: { text: 'Breathe in...', duration: 4000, scale: 1.3 },
      hold: { text: 'Hold gently...', duration: 3000, scale: 1.1 },
      exhale: { text: 'Release...', duration: 4000, scale: 0.8 },
      pause: { text: 'Rest...', duration: 3000, scale: 1.0 }
    };

    function breathingCycle() {
      const currentPhase = phases[phase];
      breathingText.textContent = currentPhase.text;
      
      // Animate breathing circle
      breathingCircle.style.transform = `scale(${currentPhase.scale})`;
      breathingCircle.style.transition = `transform ${currentPhase.duration}ms ease-in-out`;
      
      // Next phase
      setTimeout(() => {
        switch(phase) {
          case 'inhale': phase = 'hold'; break;
          case 'hold': phase = 'exhale'; break;
          case 'exhale': phase = 'pause'; break;
          case 'pause': phase = 'inhale'; break;
        }
        breathingCycle();
      }, currentPhase.duration);
    }

    // Start breathing cycle
    breathingCycle();
  }

  // Falling Leaves Animation
  function initFallingLeaves() {
    const container = document.querySelector('.falling-leaves-container');
    if (!container) return;

    const leaves = ['🍃', '🍂', '🌿', '🍀'];
    
    function createLeaf() {
      const leaf = document.createElement('div');
      leaf.className = 'falling-leaf absolute text-2xl opacity-60 pointer-events-none';
      leaf.textContent = leaves[Math.floor(Math.random() * leaves.length)];
      
      // Random starting position
      leaf.style.left = Math.random() * 100 + '%';
      leaf.style.top = '-50px';
      leaf.style.fontSize = (0.5 + Math.random() * 1) + 'rem';
      
      container.appendChild(leaf);
      
      // Animate falling
      const duration = 15000 + Math.random() * 10000;
      const sway = 30 + Math.random() * 40;
      
      leaf.animate([
        { 
          transform: 'translateY(-50px) rotate(0deg) translateX(0px)',
          opacity: 0 
        },
        { 
          transform: `translateY(${window.innerHeight + 100}px) rotate(360deg) translateX(${sway}px)`,
          opacity: 0.6,
          offset: 0.1
        },
        { 
          transform: `translateY(${window.innerHeight + 100}px) rotate(720deg) translateX(-${sway/2}px)`,
          opacity: 0.6,
          offset: 0.9
        },
        { 
          transform: `translateY(${window.innerHeight + 100}px) rotate(1080deg) translateX(0px)`,
          opacity: 0 
        }
      ], {
        duration: duration,
        easing: 'linear'
      }).onfinish = () => {
        leaf.remove();
      };
    }

    // Create leaves periodically
    function startLeafFall() {
      createLeaf();
      setTimeout(startLeafFall, 2000 + Math.random() * 3000);
    }
    
    startLeafFall();
  }

  // Ambient Light Effects
  function initAmbientEffects() {
    const orb1 = document.querySelector('.light-orb-1');
    const orb2 = document.querySelector('.light-orb-2');
    
    if (orb1) {
      orb1.style.background = 'radial-gradient(circle, rgba(168, 198, 134, 0.3) 0%, transparent 70%)';
      orb1.style.left = '20%';
      orb1.style.top = '30%';
      
      // Gentle floating animation
      orb1.animate([
        { transform: 'translate(0, 0) scale(1)', opacity: 0.3 },
        { transform: 'translate(20px, -30px) scale(1.2)', opacity: 0.5 },
        { transform: 'translate(0, 0) scale(1)', opacity: 0.3 }
      ], {
        duration: 8000,
        iterations: Infinity,
        easing: 'ease-in-out'
      });
    }
    
    if (orb2) {
      orb2.style.background = 'radial-gradient(circle, rgba(139, 69, 19, 0.2) 0%, transparent 70%)';
      orb2.style.right = '20%';
      orb2.style.bottom = '30%';
      
      // Gentle floating animation (offset)
      orb2.animate([
        { transform: 'translate(0, 0) scale(1.2)', opacity: 0.2 },
        { transform: 'translate(-15px, 20px) scale(1)', opacity: 0.4 },
        { transform: 'translate(0, 0) scale(1.2)', opacity: 0.2 }
      ], {
        duration: 10000,
        iterations: Infinity,
        easing: 'ease-in-out'
      });
    }
  }

  // Mouse interaction - gentle light following
  function initMouseInteraction() {
    let mouseLight = null;
    
    document.addEventListener('mousemove', (e) => {
      if (!mouseLight) {
        mouseLight = document.createElement('div');
        mouseLight.className = 'mouse-light fixed w-32 h-32 rounded-full pointer-events-none z-5';
        mouseLight.style.background = 'radial-gradient(circle, rgba(168, 198, 134, 0.1) 0%, transparent 70%)';
        mouseLight.style.filter = 'blur(20px)';
        mouseLight.style.transition = 'all 0.3s ease';
        document.body.appendChild(mouseLight);
      }
      
      mouseLight.style.left = (e.clientX - 64) + 'px';
      mouseLight.style.top = (e.clientY - 64) + 'px';
    });
  }

  // Enhanced button interactions
  function initButtonEffects() {
    document.addEventListener('click', (e) => {
      if (e.target.matches('.forest-button, button')) {
        // Create ripple effect
        const ripple = document.createElement('div');
        ripple.className = 'ripple absolute rounded-full bg-green-300/30 pointer-events-none';
        ripple.style.width = '20px';
        ripple.style.height = '20px';
        ripple.style.left = (e.offsetX - 10) + 'px';
        ripple.style.top = (e.offsetY - 10) + 'px';
        
        e.target.style.position = 'relative';
        e.target.appendChild(ripple);
        
        ripple.animate([
          { transform: 'scale(0)', opacity: 1 },
          { transform: 'scale(4)', opacity: 0 }
        ], {
          duration: 600,
          easing: 'ease-out'
        }).onfinish = () => {
          ripple.remove();
        };
        
        console.log('🌿 Forest interaction - feeling peaceful...');
      }
    });
  }

  // Nature sounds simulation (visual)
  function initNatureSounds() {
    const soundWaves = document.createElement('div');
    soundWaves.className = 'sound-waves fixed bottom-4 right-4 flex space-x-1 z-50';
    soundWaves.innerHTML = `
      <div class="sound-wave w-1 h-8 bg-green-400/50 rounded-full"></div>
      <div class="sound-wave w-1 h-8 bg-green-400/50 rounded-full"></div>
      <div class="sound-wave w-1 h-8 bg-green-400/50 rounded-full"></div>
      <div class="sound-wave w-1 h-8 bg-green-400/50 rounded-full"></div>
    `;
    document.body.appendChild(soundWaves);
    
    // Animate sound waves
    const waves = soundWaves.querySelectorAll('.sound-wave');
    waves.forEach((wave, index) => {
      wave.animate([
        { transform: 'scaleY(0.5)', opacity: 0.3 },
        { transform: 'scaleY(1.5)', opacity: 0.8 },
        { transform: 'scaleY(0.5)', opacity: 0.3 }
      ], {
        duration: 2000,
        iterations: Infinity,
        delay: index * 200,
        easing: 'ease-in-out'
      });
    });
  }

  // Initialize everything when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
      initForestCalm();
      initMouseInteraction();
      initButtonEffects();
      initNatureSounds();
    });
  } else {
    initForestCalm();
    initMouseInteraction();
    initButtonEffects();
    initNatureSounds();
  }

})();
