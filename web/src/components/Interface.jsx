import { useEffect, useId, useMemo, useRef, useState } from 'react'
import {
  BadgeCheck,
  Biohazard,
  Binoculars,
  CircleDot,
  ClipboardList,
  FileText,
  Radio,
  Route,
  Shield,
  ShieldCheck,
  Truck,
  Warehouse,
  Wrench,
} from 'lucide-react'
import { cardWear } from '../utils/cardWear'

const iconMap = {
  BadgeCheck,
  Biohazard,
  Binoculars,
  CircleDot,
  ClipboardList,
  FileText,
  Radio,
  Route,
  Shield,
  ShieldCheck,
  Truck,
  Warehouse,
  Wrench,
}

function titleHash(value) {
  return String(value).split('').reduce((hash, char) => {
    return (hash * 31 + char.charCodeAt(0)) % 997
  }, 7)
}

export function DataIcon({ name, className = 'h-5 w-5' }) {
  const Icon = iconMap[name] ?? CircleDot
  return <Icon className={className} strokeWidth={1.7} />
}

export function GlitchTitle({ as: Component = 'span', children, className = '', intensity = 'subtle' }) {
  const text = String(children)
  const hash = titleHash(text)
  const style = {
    '--glitch-delay': `${-(hash % 2600)}ms`,
    '--glitch-shift': `${2 + (hash % 5)}px`,
    '--glitch-slice-a': `${18 + (hash % 22)}%`,
    '--glitch-slice-b': `${62 + (hash % 26)}%`,
  }

  return (
    <Component
      className={`glitch-title glitch-title-${intensity} ${className}`}
      data-text={text}
      style={style}
    >
      <span className="glitch-title-text">{children}</span>
    </Component>
  )
}

export function SectionHeader({ eyebrow, title, text }) {
  return (
    <div className="mb-8 max-w-3xl">
      {eyebrow ? <p className="kicker">{eyebrow}</p> : null}
      <GlitchTitle as="h2" intensity="subtle" className="font-display text-4xl uppercase text-stone-100 sm:text-5xl">
        {title}
      </GlitchTitle>
      {text ? <p className="mt-4 text-base leading-7 text-stone-400 sm:text-lg">{text}</p> : null}
    </div>
  )
}

export function Panel({ children, className = '', seed }) {
  const generatedId = useId()
  const wear = cardWear(seed ?? generatedId, className)

  return (
    <div className={`archive-panel ${wear.className}`} style={wear.style} data-serial={wear.serial}>
      {children}
    </div>
  )
}

export function Tag({ children, tone = 'default' }) {
  const toneClass =
    tone === 'danger'
      ? 'border-red-500/35 bg-red-500/10 text-red-200'
      : tone === 'green'
        ? 'border-emerald-500/35 bg-emerald-500/10 text-emerald-200'
        : tone === 'amber'
          ? 'border-amber-400/35 bg-amber-400/10 text-amber-200'
          : 'border-stone-700 bg-stone-900/70 text-stone-300'

  return (
    <span className={`inline-flex items-center rounded-sm border px-2.5 py-1 text-xs uppercase ${toneClass}`}>
      {children}
    </span>
  )
}

export function TerminalPanel({ lines }) {
  const bodyRef = useRef(null)
  const commands = useMemo(() => lines.map((line) => line.replace(/^>\s?/, '')), [lines])
  const sessions = useMemo(() => {
    const outputs = [
      ['link established', 'handshake: accepted'],
      ['channel locked', 'noise floor: -73db'],
      ['index mounted', 'records available: 07'],
      ['clearance granted', 'operator role: internal'],
    ]

    return commands.map((command, index) => ({
      command,
      output: outputs[index] ?? ['ok'],
    }))
  }, [commands])
  const [activeIndex, setActiveIndex] = useState(0)
  const [typedCount, setTypedCount] = useState(0)
  const [completedCount, setCompletedCount] = useState(0)

  useEffect(() => {
    if (!sessions.length) return undefined

    const activeCommand = sessions[activeIndex]?.command ?? ''
    const isTyping = typedCount < activeCommand.length
    const isLastCommand = activeIndex >= sessions.length - 1

    const timeout = window.setTimeout(
      () => {
        if (isTyping) {
          setTypedCount((value) => value + 1)
          return
        }

        setCompletedCount(activeIndex + 1)

        if (isLastCommand) {
          window.setTimeout(() => {
            setActiveIndex(0)
            setTypedCount(0)
            setCompletedCount(0)
          }, 2800)
          return
        }

        window.setTimeout(() => {
          setActiveIndex((value) => value + 1)
          setTypedCount(0)
        }, 540)
      },
      isTyping ? 34 + ((typedCount + activeIndex) % 5) * 18 : 520,
    )

    return () => window.clearTimeout(timeout)
  }, [activeIndex, sessions, typedCount])

  const completedSessions = sessions.slice(0, completedCount)
  const activeCommand = sessions[activeIndex]?.command ?? ''
  const typedCommand = activeCommand.slice(0, typedCount)
  const showActivePrompt = completedCount < sessions.length

  useEffect(() => {
    if (!bodyRef.current) return
    bodyRef.current.scrollTop = bodyRef.current.scrollHeight
  }, [activeIndex, completedCount, typedCount])

  return (
    <div className="terminal-panel" aria-label="Vexel terminal window">
      <div className="terminal-header">
        <div className="terminal-tab">
          <span>TERMINAL</span>
        </div>
        <div className="terminal-session">
          <span className="terminal-cube">vx</span>
          <span>bash - vexel-db</span>
        </div>
        <div className="terminal-controls" aria-hidden="true">
          <span />
          <span />
          <span />
        </div>
      </div>

      <div className="terminal-body" ref={bodyRef}>
        <div className="terminal-meta">
          <span className="terminal-path">~/vexel/internal</span>
          <span className="terminal-time">took 508ms</span>
        </div>

        {completedSessions.map((session, index) => (
          <div className="terminal-block" key={session.command}>
            <div className="terminal-row terminal-row-executed" style={{ '--delay': `${index * 0.2}s` }}>
              <span className="terminal-dot" aria-hidden="true" />
              <span className="terminal-prompt" aria-hidden="true">
                ❯
              </span>
              <span className="terminal-command">{session.command}</span>
            </div>
            {session.output.map((output) => (
              <div className="terminal-output" key={`${session.command}-${output}`}>
                {output}
              </div>
            ))}
          </div>
        ))}

        {showActivePrompt ? (
          <div className="terminal-row terminal-row-active">
            <span className="terminal-dot" aria-hidden="true" />
            <span className="terminal-prompt" aria-hidden="true">
              ❯
            </span>
            <span className="terminal-command terminal-command-active">
              {typedCommand}
              <span className="terminal-cursor" aria-hidden="true" />
            </span>
          </div>
        ) : null}

        {showActivePrompt ? (
          <div className="terminal-process">executing protocol...</div>
        ) : (
          <>
            <div className="terminal-status">
              <span className="terminal-path">~/vexel/internal</span>
              <span className="terminal-branch">main</span>
              <span className="terminal-dirty">[secure]</span>
            </div>
            <div className="terminal-row terminal-row-active">
              <span className="terminal-dot" aria-hidden="true" />
              <span className="terminal-prompt" aria-hidden="true">
                ❯
              </span>
              <span className="terminal-command">
                awaiting operator input
                <span className="terminal-cursor" aria-hidden="true" />
              </span>
            </div>
          </>
        )}
      </div>
    </div>
  )
}
