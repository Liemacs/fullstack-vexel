import { useState } from 'react'
import { MapPin } from 'lucide-react'
import { Panel, Tag } from './Interface'

function riskTone(risk) {
  if (risk === 'Критический' || risk === 'Высокий') return 'danger'
  if (risk === 'Контроль') return 'green'
  return 'amber'
}

export function RegionMap({ points }) {
  const [selectedId, setSelectedId] = useState(points[0]?.id)
  const selected = points.find((point) => point.id === selectedId) ?? points[0]

  return (
    <div className="grid gap-6 lg:grid-cols-[1.5fr_0.8fr]">
      <div className="map-frame">
        <div className="absolute inset-0 map-grid" />
        <div className="absolute left-[12%] top-[18%] h-[2px] w-[68%] rotate-[23deg] bg-amber-300/25" />
        <div className="absolute left-[25%] top-[68%] h-[2px] w-[58%] -rotate-[16deg] bg-stone-500/25" />
        <div className="absolute left-[48%] top-[48%] h-[2px] w-[30%] rotate-[47deg] bg-red-400/20" />

        {points.map((point) => (
          <button
            key={point.id}
            type="button"
            className={`map-point ${selectedId === point.id ? 'map-point-active' : ''}`}
            style={{ left: `${point.x}%`, top: `${point.y}%` }}
            onClick={() => setSelectedId(point.id)}
            aria-label={point.name}
          >
            <MapPin className="h-5 w-5" />
            <span>{point.name}</span>
          </button>
        ))}
      </div>

      <Panel seed={`map-${selected.id}`} className="self-stretch">
        <p className="kicker">карта региона</p>
        <h3 className="mt-3 font-display text-4xl uppercase text-stone-100">{selected.name}</h3>
        <p className="mt-2 text-sm uppercase text-amber-300">{selected.type}</p>
        <div className="mt-5">
          <Tag tone={riskTone(selected.risk)}>{selected.risk}</Tag>
        </div>
        <p className="mt-6 text-sm leading-7 text-stone-400">{selected.text}</p>
        <div className="mt-8 border-t border-stone-800 pt-5 font-mono text-xs uppercase text-stone-500">
          route scan / grid {selected.x}:{selected.y}
        </div>
      </Panel>
    </div>
  )
}
