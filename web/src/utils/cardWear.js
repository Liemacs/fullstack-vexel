const panelTypes = [
  'card-type-a',
  'card-type-b',
  'card-type-c',
  'damaged-card',
  'reinforced-card',
  'patched-card',
  'newer-card',
  'scarred-card',
]

const hoverTypes = ['hover-glow', 'hover-flicker', 'hover-cold', 'hover-lift']

const textureTypes = ['texture-grunge', 'texture-wall', 'texture-ash', 'texture-paper']

function hashSeed(seed) {
  const value = String(seed)
  let hash = 2166136261

  for (let index = 0; index < value.length; index += 1) {
    hash ^= value.charCodeAt(index)
    hash = Math.imul(hash, 16777619)
  }

  return hash >>> 0
}

function ranged(hash, min, max, salt = 0) {
  const shifted = (hash >>> salt) & 255
  return min + (shifted / 255) * (max - min)
}

export function cardWear(seed, extraClass = '') {
  const hash = hashSeed(seed)
  const type = panelTypes[hash % panelTypes.length]
  const hover = hoverTypes[(hash >>> 4) % hoverTypes.length]
  const texture = textureTypes[(hash >>> 7) % textureTypes.length]
  const serial = `VX-${String(hash % 997).padStart(3, '0')}`

  return {
    className: `${type} ${texture} ${hover} ${extraClass}`.trim(),
    serial,
    style: {
      '--wear-rotate': `${ranged(hash, -0.55, 0.55, 3).toFixed(2)}deg`,
      '--stain-x': `${ranged(hash, 10, 82, 2).toFixed(0)}%`,
      '--stain-y': `${ranged(hash, 14, 78, 9).toFixed(0)}%`,
      '--wear-opacity': ranged(hash, 0.16, 0.38, 18).toFixed(2),
      '--texture-x': `${ranged(hash, 0, 100, 6).toFixed(0)}%`,
      '--texture-y': `${ranged(hash, 0, 100, 11).toFixed(0)}%`,
      '--texture-scale': `${ranged(hash, 34, 58, 14).toFixed(0)}rem`,
      '--plate-offset': `${ranged(hash, 8, 26, 21).toFixed(0)}px`,
    },
  }
}
