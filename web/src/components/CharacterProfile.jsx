import { ArrowLeft, Radio, ShieldCheck } from 'lucide-react'
import { Panel, Tag } from './Interface'

export function CharacterProfile({ member }) {
  return (
    <div>
      <section className="relative min-h-[62svh] overflow-hidden border-b border-stone-800">
        <img src={member.image} alt={member.name} className="absolute inset-0 h-full w-full object-cover grayscale" />
        <div className="absolute inset-0 bg-gradient-to-r from-black via-black/80 to-black/25" />
        <div className="absolute inset-0 bg-[linear-gradient(90deg,rgba(216,160,43,0.11)_1px,transparent_1px),linear-gradient(0deg,rgba(255,255,255,0.04)_1px,transparent_1px)] bg-[size:76px_76px]" />
        <div className="relative mx-auto flex min-h-[62svh] max-w-7xl flex-col justify-end px-4 pb-14 sm:px-6 lg:px-8">
          <a href="/people" className="mb-8 inline-flex w-fit items-center gap-2 border border-stone-700 bg-black/60 px-4 py-2 text-sm uppercase text-stone-300 transition hover:border-amber-300/60 hover:text-amber-200">
            <ArrowLeft className="h-4 w-4" />
            Люди Векселя
          </a>
          <p className="kicker">личное досье / {member.callSign}</p>
          <h1 className="mt-3 font-display text-6xl uppercase text-stone-100 sm:text-8xl">
            {member.name}
          </h1>
          <p className="mt-4 max-w-2xl font-lore text-lg leading-8 text-stone-300">{member.quote}</p>
        </div>
      </section>

      <section className="site-section">
        <div className="grid gap-6 lg:grid-cols-[0.78fr_1.22fr]">
          <Panel seed={`profile-${member.slug}-photo`}>
            <img src={member.image} alt={member.name} className="aspect-[4/5] w-full object-cover" />
            <div className="mt-5 grid gap-3 text-sm">
              {[
                ['Имя', member.name],
                ['Возраст', member.age],
                ['Статус', member.status],
                ['Профессия', member.role],
              ].map(([label, value]) => (
                <div key={label} className="flex items-center justify-between gap-4 border-b border-stone-800 pb-3">
                  <span className="text-stone-500">{label}</span>
                  <span className="text-right text-stone-100">{value}</span>
                </div>
              ))}
            </div>
          </Panel>

          <div className="grid gap-6">
            <Panel seed={`profile-${member.slug}-bio`}>
              <p className="kicker">bio</p>
              <h2 className="mt-3 font-display text-4xl uppercase text-stone-100">Биография</h2>
              <p className="mt-4 font-lore text-base leading-8 text-stone-400">{member.bio}</p>
            </Panel>

            <Panel seed={`profile-${member.slug}-skills`}>
              <p className="kicker">skills</p>
              <h2 className="mt-3 font-display text-4xl uppercase text-stone-100">Навыки</h2>
              <div className="mt-6 grid gap-4">
                {member.skills.map((skill) => (
                  <div key={skill.name}>
                    <div className="mb-2 flex justify-between text-sm">
                      <span className="text-stone-300">{skill.name}</span>
                      <span className="font-mono text-amber-300">{skill.value}%</span>
                    </div>
                    <div className="h-2 bg-stone-900">
                      <div className="h-full bg-gradient-to-r from-amber-500 to-orange-300" style={{ width: `${skill.value}%` }} />
                    </div>
                  </div>
                ))}
              </div>
            </Panel>
          </div>
        </div>

        <div className="mt-6 grid gap-6 lg:grid-cols-3">
          <Panel seed={`profile-${member.slug}-gear`}>
            <ShieldCheck className="h-6 w-6 text-amber-300" />
            <h2 className="mt-4 font-display text-3xl uppercase text-stone-100">Снаряжение</h2>
            <div className="mt-5 flex flex-wrap gap-2">
              {member.gear.map((item) => (
                <Tag key={item}>{item}</Tag>
              ))}
            </div>
          </Panel>

          <Panel seed={`profile-${member.slug}-character`}>
            <Radio className="h-6 w-6 text-amber-300" />
            <h2 className="mt-4 font-display text-3xl uppercase text-stone-100">Характер</h2>
            <p className="mt-4 font-lore text-sm leading-7 text-stone-400">{member.character}</p>
          </Panel>

          <Panel seed={`profile-${member.slug}-connections`}>
            <h2 className="font-display text-3xl uppercase text-stone-100">Связи</h2>
            <div className="mt-5 grid gap-2">
              {member.connections.map((connection) => (
                <div key={connection} className="border-l-2 border-amber-400/40 bg-stone-950/60 px-3 py-2 text-sm text-stone-300">
                  {connection}
                </div>
              ))}
            </div>
          </Panel>
        </div>

        <div className="mt-6">
          <Panel seed={`profile-${member.slug}-history`}>
            <p className="kicker">history</p>
            <h2 className="mt-3 font-display text-4xl uppercase text-stone-100">История в Векселе</h2>
            <p className="mt-4 font-lore text-sm leading-7 text-stone-400">{member.vexelHistory}</p>
          </Panel>
        </div>
      </section>
    </div>
  )
}
