import React, { useState, useEffect } from 'react'
import { motion } from 'framer-motion'

const FallingLeaves = () => {
  const [leaves, setLeaves] = useState([])

  useEffect(() => {
    // Generate gentle falling leaves
    const generateLeaves = () => {
      const newLeaves = []
      for (let i = 0; i < 12; i++) {
        newLeaves.push({
          id: i,
          x: Math.random() * 100,
          delay: Math.random() * 10,
          duration: 15 + Math.random() * 10,
          size: 0.5 + Math.random() * 1,
          rotation: Math.random() * 360,
          type: Math.floor(Math.random() * 4), // Different leaf types
        })
      }
      setLeaves(newLeaves)
    }

    generateLeaves()

    // Regenerate leaves periodically for continuous effect
    const interval = setInterval(() => {
      generateLeaves()
    }, 20000)

    return () => clearInterval(interval)
  }, [])

  const leafTypes = [
    '🍃', // Simple leaf
    '🍂', // Autumn leaf
    '🌿', // Herb
    '🍀'  // Four leaf clover (rare, for luck!)
  ]

  return (
    <div className="fixed inset-0 pointer-events-none z-10">
      {leaves.map((leaf) => (
        <motion.div
          key={leaf.id}
          className="absolute text-2xl opacity-60"
          style={{
            left: `${leaf.x}%`,
            fontSize: `${leaf.size}rem`,
          }}
          initial={{
            y: -100,
            rotate: leaf.rotation,
            opacity: 0,
          }}
          animate={{
            y: window.innerHeight + 100,
            rotate: leaf.rotation + 360,
            opacity: [0, 0.6, 0.6, 0],
            x: [0, 30, -20, 10, 0], // Gentle swaying motion
          }}
          transition={{
            duration: leaf.duration,
            delay: leaf.delay,
            ease: "linear",
            repeat: Infinity,
            x: {
              duration: leaf.duration / 3,
              repeat: Infinity,
              repeatType: "reverse",
              ease: "easeInOut",
            }
          }}
        >
          {leafTypes[leaf.type]}
        </motion.div>
      ))}
    </div>
  )
}

export default FallingLeaves
