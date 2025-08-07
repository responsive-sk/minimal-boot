import React, { useState, useEffect } from 'react'
import { motion } from 'framer-motion'

const NatureHero = () => {
  const [breathingPhase, setBreathingPhase] = useState('inhale')
  const [mousePosition, setMousePosition] = useState({ x: 0, y: 0 })

  useEffect(() => {
    // Breathing guide cycle
    const breathingCycle = () => {
      setBreathingPhase('inhale')
      setTimeout(() => setBreathingPhase('hold'), 4000)
      setTimeout(() => setBreathingPhase('exhale'), 7000)
      setTimeout(() => setBreathingPhase('hold'), 11000)
    }

    breathingCycle()
    const interval = setInterval(breathingCycle, 14000)

    return () => clearInterval(interval)
  }, [])

  useEffect(() => {
    const handleMouseMove = (e) => {
      setMousePosition({
        x: (e.clientX / window.innerWidth) * 100,
        y: (e.clientY / window.innerHeight) * 100,
      })
    }

    window.addEventListener('mousemove', handleMouseMove)
    return () => window.removeEventListener('mousemove', handleMouseMove)
  }, [])

  const features = [
    {
      icon: '🌱',
      title: 'Growth',
      description: 'Gentle progress, like nature - slow, steady, and sustainable for your mind and soul.',
      color: 'forest-sage'
    },
    {
      icon: '🍃',
      title: 'Breathe',
      description: 'Deep breathing exercises integrated into the interface for natural stress relief.',
      color: 'forest-light'
    },
    {
      icon: '🌳',
      title: 'Stability',
      description: 'Rooted like ancient trees - reliable, strong, and peaceful digital foundation.',
      color: 'forest-main'
    }
  ]

  const breathingInstructions = {
    inhale: 'Breathe in slowly... 🌬️',
    hold: 'Hold gently... 🫁',
    exhale: 'Release and let go... 🍃'
  }

  return (
    <section className="relative min-h-screen flex items-center justify-center overflow-hidden pt-16">
      {/* Gentle Mouse Following Light */}
      <motion.div
        className="absolute w-96 h-96 rounded-full blur-3xl pointer-events-none"
        style={{
          background: `radial-gradient(circle, rgba(168, 198, 134, 0.15) 0%, transparent 70%)`,
          left: `${mousePosition.x}%`,
          top: `${mousePosition.y}%`,
          transform: 'translate(-50%, -50%)',
        }}
        animate={{
          scale: [1, 1.2, 1],
          opacity: [0.3, 0.5, 0.3],
        }}
        transition={{
          duration: 6,
          repeat: Infinity,
          ease: "easeInOut"
        }}
      />

      {/* Content */}
      <div className="relative z-10 max-w-6xl mx-auto px-6 text-center">
        {/* Breathing Guide */}
        <motion.div 
          className="mb-8"
          initial={{ opacity: 0, y: 30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 2, ease: "easeOut" }}
        >
          <motion.div
            className="w-20 h-20 mx-auto mb-4 rounded-full bg-gradient-to-br from-forest-sage to-forest-light flex items-center justify-center text-3xl"
            animate={{
              scale: breathingPhase === 'inhale' ? 1.3 : breathingPhase === 'exhale' ? 0.8 : 1.1,
            }}
            transition={{
              duration: breathingPhase === 'inhale' ? 4 : breathingPhase === 'exhale' ? 4 : 3,
              ease: "easeInOut"
            }}
          >
            🫁
          </motion.div>
          <p className="nature-text text-lg font-calm">
            {breathingInstructions[breathingPhase]}
          </p>
        </motion.div>

        {/* Main Heading */}
        <motion.h1 
          className="text-5xl md:text-7xl font-bold mb-6 font-nature"
          initial={{ opacity: 0, y: 50 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 2, delay: 0.5, ease: "easeOut" }}
        >
          <span className="forest-glow block mb-2">Digital Forest</span>
          <span className="nature-text block">Healing Space</span>
        </motion.h1>
        
        <motion.div 
          className="text-xl md:text-2xl nature-text/80 mb-12 max-w-3xl mx-auto leading-relaxed font-calm"
          initial={{ opacity: 0, y: 30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 2, delay: 1, ease: "easeOut" }}
        >
          Built with <strong>React</strong>, <strong>Framer Motion</strong>, and <strong>Nature's Wisdom</strong> for deep relaxation and digital wellness.
        </motion.div>
        
        {/* Nature Feature Cards */}
        <div className="grid md:grid-cols-3 gap-8 mb-12">
          {features.map((feature, index) => (
            <motion.div
              key={feature.title}
              className="breathing-card group cursor-pointer"
              initial={{ opacity: 0, y: 50 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 1, delay: 1.5 + index * 0.2, ease: "easeOut" }}
              whileHover={{ 
                scale: 1.05, 
                y: -10,
                transition: { duration: 0.3 }
              }}
            >
              <motion.div 
                className="text-5xl mb-4"
                animate={{ 
                  rotate: [0, 5, -5, 0],
                  scale: [1, 1.1, 1]
                }}
                transition={{ 
                  duration: 4 + index, 
                  repeat: Infinity, 
                  ease: "easeInOut" 
                }}
              >
                {feature.icon}
              </motion.div>
              <h3 className="forest-glow font-semibold mb-3 text-xl font-calm">
                {feature.title}
              </h3>
              <p className="nature-text/70 text-sm leading-relaxed">
                {feature.description}
              </p>
              
              {/* Gentle hover glow */}
              <motion.div
                className={`absolute inset-0 bg-${feature.color}/10 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500`}
              />
            </motion.div>
          ))}
        </div>
        
        {/* Peaceful CTA Buttons */}
        <motion.div 
          className="flex flex-col sm:flex-row gap-6 justify-center"
          initial={{ opacity: 0, y: 30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 1, delay: 2.5, ease: "easeOut" }}
        >
          <motion.button
            className="forest-button text-lg px-8 py-4 font-calm"
            whileHover={{ scale: 1.05, y: -2 }}
            whileTap={{ scale: 0.95 }}
            onClick={() => {
              console.log('🌲 Starting forest meditation...')
              // Could trigger breathing exercise or nature sounds
            }}
          >
            🧘‍♀️ Start Meditation
          </motion.button>
          <motion.button
            className="px-8 py-4 bg-forest-main/30 hover:bg-forest-main/50 text-nature-cream font-medium rounded-xl transition-all duration-500 backdrop-blur-sm border border-forest-sage/30 text-lg font-calm"
            whileHover={{ scale: 1.05, y: -2 }}
            whileTap={{ scale: 0.95 }}
            onClick={() => window.open('/demo', '_blank')}
          >
            🌿 Explore Nature
          </motion.button>
        </motion.div>

        {/* Gentle Scroll Indicator */}
        <motion.div
          className="absolute bottom-8 left-1/2 transform -translate-x-1/2 cursor-pointer"
          animate={{ y: [0, 10, 0] }}
          transition={{ duration: 3, repeat: Infinity, ease: "easeInOut" }}
          onClick={() => window.scrollTo({ top: window.innerHeight, behavior: 'smooth' })}
        >
          <div className="w-8 h-12 border-2 border-forest-sage/50 rounded-full flex justify-center backdrop-blur-sm">
            <motion.div 
              className="w-1 h-3 bg-forest-light rounded-full mt-2"
              animate={{ opacity: [0.5, 1, 0.5] }}
              transition={{ duration: 2, repeat: Infinity, ease: "easeInOut" }}
            />
          </div>
          <p className="nature-text/60 text-xs mt-2 font-calm">Scroll gently</p>
        </motion.div>
      </div>

      {/* Ambient Nature Sounds Visualizer */}
      <div className="absolute bottom-4 right-4 flex space-x-1">
        {[...Array(4)].map((_, i) => (
          <motion.div
            key={i}
            className="sound-wave h-8"
            animate={{ 
              scaleY: [0.5, 1.5, 0.5],
              opacity: [0.3, 0.8, 0.3]
            }}
            transition={{
              duration: 2,
              repeat: Infinity,
              delay: i * 0.2,
              ease: "easeInOut"
            }}
          />
        ))}
      </div>
    </section>
  )
}

export default NatureHero
