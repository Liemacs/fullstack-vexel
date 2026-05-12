import { useEffect, useRef, useState } from 'react'
import {
  ArrowRight,
  FileSearch,
  MessageSquareWarning,
  Pause,
  Play,
  Shield,
} from 'lucide-react'
import { CharacterCard, ContractCard, VehicleGroup } from './components/Cards'
import { CharacterProfile } from './components/CharacterProfile'
import { DataIcon, Panel, SectionHeader, Tag, TerminalPanel } from './components/Interface'
import { RegionMap } from './components/RegionMap'
import { Shell } from './components/Shell'
import { cardWear } from './utils/cardWear'
import {
  codeRules,
  contracts,
  anthemAudio,
  leadershipCards,
  mapPoints,
  members,
  navItems,
  placeholderImage,
  principles,
  services,
  structure,
  terminalLines,
  timeline,
  vehicles,
} from './data/vexelData'

function getRoute() {
  const legacyHash = window.location.hash.replace(/^#\/?/, '')

  if (legacyHash) {
    const normalizedPath = legacyHash === 'home' ? '/' : `/${legacyHash}`
    window.history.replaceState({}, '', normalizedPath)
  }

  const path = window.location.pathname.replace(/^\/+|\/+$/g, '')

  if (!path) {
    return { page: 'home', slug: null }
  }

  if (path.startsWith('profile/')) {
    return { page: 'profile', slug: path.replace('profile/', '') }
  }

  const page = path.split('/')[0]
  const isKnownPage = navItems.some((item) => item.id === page)

  return { page: isKnownPage ? page : 'home', slug: null }
}

function App() {
  const [route, setRoute] = useState(getRoute)

  useEffect(() => {
    const onPopState = () => setRoute(getRoute())
    const onClick = (event) => {
      const link = event.target.closest('a[href]')

      if (!link) {
        return
      }

      const url = new URL(link.href)
      const isInternalLink = url.origin === window.location.origin

      if (!isInternalLink || link.target || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
        return
      }

      event.preventDefault()
      window.history.pushState({}, '', `${url.pathname}${url.search}`)
      setRoute(getRoute())
      window.scrollTo({ top: 0, left: 0 })
    }

    window.addEventListener('popstate', onPopState)
    document.addEventListener('click', onClick)

    return () => {
      window.removeEventListener('popstate', onPopState)
      document.removeEventListener('click', onClick)
    }
  }, [])

  useEffect(() => {
    window.scrollTo({ top: 0, left: 0 })
  }, [route.page, route.slug])

  const activePage = route.page === 'profile' ? 'people' : route.page
  const profile = route.page === 'profile' ? members.find((member) => member.slug === route.slug) : null

  return (
    <Shell activePage={activePage}>
      {route.page === 'profile' && profile ? <CharacterProfile member={profile} /> : <Page page={route.page} />}
    </Shell>
  )
}

function Page({ page }) {
  switch (page) {
    case 'history':
      return <HistoryPage />
    case 'principles':
      return <PrinciplesPage />
    case 'structure':
      return <StructurePage />
    case 'people':
      return <PeoplePage />
    case 'contracts':
      return <ContractsPage />
    case 'map':
      return <MapPage />
    case 'gear':
      return <GearPage />
    case 'code':
      return <CodePage />
    case 'home':
    default:
      return <HomePage />
  }
}

function AnthemButton() {
  const audioRef = useRef(null)
  const [isPlaying, setIsPlaying] = useState(false)

  function toggleAnthem() {
    const audio = audioRef.current
    if (!audio) return

    if (isPlaying) {
      audio.pause()
      setIsPlaying(false)
      return
    }

    audio.play()
      .then(() => setIsPlaying(true))
      .catch(() => setIsPlaying(false))
  }

  return (
    <>
      <button
        type="button"
        className={`anthem-action ${isPlaying ? 'anthem-action-active' : ''}`}
        onClick={toggleAnthem}
        aria-label={isPlaying ? 'Остановить гимн' : 'Включить гимн'}
      >
        {isPlaying ? <Pause className="h-5 w-5" /> : <Play className="h-5 w-5" />}
        {isPlaying ? 'Остановить гимн' : 'Слушать гимн'}
      </button>
      <audio
        ref={audioRef}
        src={anthemAudio}
        preload="metadata"
        onEnded={() => setIsPlaying(false)}
        onPause={() => setIsPlaying(false)}
        onPlay={() => setIsPlaying(true)}
      />
    </>
  )
}

function HomePage() {
  return (
    <>
      <section className="hero-section">
        <img src={placeholderImage} alt="" className="absolute inset-0 h-full w-full object-cover" />
        <div className="absolute inset-0 bg-gradient-to-r from-black via-black/75 to-black/20" />
        <div className="absolute inset-0 bg-[linear-gradient(90deg,rgba(216,160,43,0.12)_1px,transparent_1px),linear-gradient(0deg,rgba(255,255,255,0.035)_1px,transparent_1px)] bg-[size:96px_96px]" />

        <div className="relative mx-auto grid min-h-[86svh] max-w-7xl items-end gap-10 px-4 pb-10 pt-16 sm:px-6 lg:grid-cols-[1.1fr_0.75fr] lg:px-8">
          <div className="pb-8">
            <p className="kicker">внутренняя база / channel 13</p>
            <h1 className="mt-4 font-display text-7xl uppercase leading-none text-stone-100 sm:text-9xl">
              Вексель
            </h1>
            <p className="mt-6 max-w-2xl text-2xl font-semibold text-amber-200">
              Задача должна быть оплачена.
            </p>
            <p className="mt-2 max-w-2xl text-xl text-stone-300">
              Человек Векселя превыше чужака.
            </p>

            <div className="mt-9 flex flex-wrap gap-3">
              <a href="/history" className="primary-action">
                 История
              </a>
              <a href="/people" className="secondary-action">
                Просмотреть досье
              </a>
              <AnthemButton />
            </div>
          </div>

          <div className="grid gap-4">
            <TerminalPanel lines={terminalLines} />
            <div className="grid grid-cols-3 gap-3">
              {[
                ['13', 'канал'],
                ['04', 'контрактов'],
                ['07', 'досье'],
              ].map(([value, label]) => (
                <div key={label} className="border border-stone-800 bg-black/55 p-4 text-center">
                  <p className="font-display text-4xl text-amber-200">{value}</p>
                  <p className="text-xs uppercase text-stone-500">{label}</p>
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>

      <section className="site-section">
        <SectionHeader
          eyebrow="краткая справка"
          title="Кто такие Вексель"
          text="Вексель - нейтральная наемническая сеть, которая держит дороги, сопровождает грузы, чинит технику и выживает там, где обычные лагеря распадаются от страха."
        />
        <div className="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
          <Panel seed="home-identity">
            <p className="text-lg leading-8 text-stone-300">
              Они не называют себя армией и не продают спасение. Вексель работает контрактами:
              охрана, зачистка, разведка, логистика, ремонт и вывод людей из зон, куда уже не
              ходят одиночки. Их сила не в героизме, а в порядке, учете и способности вернуться.
            </p>
          </Panel>
          <Panel seed="home-notes">
            <div className="grid gap-3 text-sm text-stone-400">
              {['Нейтралитет до оплаты', 'Свои важнее клиента', 'Радиоэфир держится круглосуточно', 'Каждый выход фиксируется в архиве'].map((item) => (
                <div key={item} className="flex items-center gap-3 border-b border-stone-800 pb-3 last:border-0 last:pb-0">
                  <Shield className="h-4 w-4 text-amber-300" />
                  <span>{item}</span>
                </div>
              ))}
            </div>
          </Panel>
        </div>
      </section>

      <section className="site-section border-y border-stone-800 bg-black/25">
        <SectionHeader eyebrow="три правила" title="Принципы" />
        <PrincipleGrid />
      </section>

      <section className="site-section">
        <SectionHeader eyebrow="услуги" title="Что делает Вексель" />
        <ServiceGrid />
      </section>

      <section className="site-section border-y border-stone-800 bg-black/25">
        <SectionHeader eyebrow="ключевые фигуры" title="Люди и власть" />
        <div className="grid gap-5 md:grid-cols-3">
          {leadershipCards.map((card) => (
            <LeadershipCard key={card.title} card={card} />
          ))}
        </div>
      </section>

    </>
  )
}

function PrincipleGrid() {
  return (
    <div className="grid gap-5 md:grid-cols-3">
      {principles.map((principle, index) => (
        <Panel key={principle.id} seed={`principle-${principle.id}`}>
          <p className="font-mono text-sm text-amber-300">0{index + 1}</p>
          <h3 className="mt-4 font-display text-3xl uppercase text-stone-100">{principle.title}</h3>
          <p className="mt-4 text-sm leading-7 text-stone-400">{principle.text}</p>
        </Panel>
      ))}
    </div>
  )
}

function ServiceGrid() {
  return (
    <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
      {services.map((service) => (
        <Panel key={service.title} seed={`service-${service.title}`}>
          <div className="mb-5 grid h-11 w-11 place-items-center border border-amber-400/35 bg-amber-400/10 text-amber-200">
            <DataIcon name={service.icon} />
          </div>
          <h3 className="font-display text-3xl uppercase text-stone-100">{service.title}</h3>
          <p className="mt-3 text-sm leading-6 text-stone-400">{service.text}</p>
        </Panel>
      ))}
    </div>
  )
}

function LeadershipCard({ card }) {
  const wear = cardWear(`leadership-${card.title}`, 'group archive-panel block')

  return (
    <a href={card.href} className={wear.className} style={wear.style} data-serial={wear.serial}>
      <p className="text-sm uppercase text-amber-300">{card.subtitle}</p>
      <h3 className="mt-3 font-display text-4xl uppercase text-stone-100">{card.title}</h3>
      <p className="mt-4 text-sm leading-6 text-stone-400">{card.text}</p>
      <span className="mt-6 inline-flex items-center gap-2 text-sm font-semibold uppercase text-amber-300">
        Открыть <ArrowRight className="h-4 w-4 transition group-hover:translate-x-1" />
      </span>
    </a>
  )
}

function HistoryPage() {
  const [selectedYear, setSelectedYear] = useState(timeline[0].year)
  const selectedEntry = timeline.find((item) => item.year === selectedYear) ?? timeline[0]

  return (
    <section className="site-section">
      <SectionHeader
        eyebrow="архив происхождения"
        title="История Векселя"
        text="Неофициальная хроника от первых лагерей до современной контрактной сети."
      />

      <div className="grid gap-6 lg:grid-cols-[0.8fr_1.2fr]">
        <Panel seed="history-timeline" className="lg:sticky lg:top-24 lg:self-start">
          <h3 className="font-display text-4xl uppercase text-stone-100">Timeline</h3>
          <div className="mt-6 grid gap-5">
            {timeline.map((item) => (
              <button
                type="button"
                key={item.year}
                className={`group relative border-l-2 pl-5 text-left transition ${
                  selectedEntry.year === item.year
                    ? 'border-amber-300'
                    : 'border-amber-400/30 hover:border-amber-300/70'
                }`}
                onClick={() => setSelectedYear(item.year)}
              >
                <span
                  className={`absolute -left-[7px] top-1 h-3 w-3 transition ${
                    selectedEntry.year === item.year ? 'bg-amber-200' : 'bg-amber-300 group-hover:bg-amber-200'
                  }`}
                />
                <p className="font-mono text-sm text-amber-300">{item.year}</p>
                <h4 className="mt-1 font-semibold text-stone-100">{item.title}</h4>
                <p className="mt-2 text-sm leading-6 text-stone-500">{item.text}</p>
                <p className="mt-3 font-mono text-xs uppercase text-stone-600">
                  {item.chapters.length} глав
                </p>
              </button>
            ))}
          </div>
        </Panel>

        <div className="grid gap-5">
          <Panel seed={`history-year-${selectedEntry.year}`}>
            <p className="font-mono text-sm text-amber-300">{selectedEntry.year}</p>
            <h3 className="mt-3 font-display text-5xl uppercase text-stone-100">
              {selectedEntry.title}
            </h3>
            <p className="mt-4 font-lore text-base leading-8 text-stone-400">{selectedEntry.text}</p>
          </Panel>

          {selectedEntry.chapters.map((chapter) => (
            <Panel key={chapter.chapter} seed={`history-${selectedEntry.year}-${chapter.chapter}`}>
              <p className="font-mono text-sm text-amber-300">
                {selectedEntry.year} / CAPITOL {chapter.chapter}
              </p>
              <h3 className="mt-3 font-display text-4xl uppercase text-stone-100">{chapter.title}</h3>
              <p className="mt-4 font-lore text-base leading-8 text-stone-400">{chapter.text}</p>
            </Panel>
          ))}
        </div>
      </div>
    </section>
  )
}

function PrinciplesPage() {
  return (
    <section className="site-section">
      <SectionHeader
        eyebrow="основа поведения"
        title="Принципы"
        text="Три правила, которые повторяют новичкам до первого выхода и проверяют после каждого возвращения."
      />
      <PrincipleGrid />
      <div className="mt-8">
        <Panel seed="principle-council-note">
          <div className="flex items-center gap-3">
            <MessageSquareWarning className="h-6 w-6 text-amber-300" />
            <h3 className="font-display text-4xl uppercase text-stone-100">Заметка Совета</h3>
          </div>
          <p className="mt-4 max-w-3xl font-lore text-base leading-8 text-stone-400">
            Вексель сохраняет нейтралитет, пока клиент не пытается купить чужую жизнь дешевле
            собственной выгоды. После этого он перестает быть клиентом и становится угрозой маршруту.
          </p>
        </Panel>
      </div>
    </section>
  )
}

function StructurePage() {
  return (
    <section className="site-section">
      <SectionHeader
        eyebrow="организация"
        title="Структура"
        text="Вексель держится на простой вертикали: лидер принимает риск, Совет держит систему, рабочие группы выполняют контракт, тыл делает возвращение возможным."
      />

      <div className="grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
        <Panel seed="structure-leader">
          <p className="kicker">{structure.leader.title}</p>
          <h3 className="mt-3 font-display text-5xl uppercase text-stone-100">{structure.leader.name}</h3>
          <p className="mt-5 font-lore text-base leading-8 text-stone-400">{structure.leader.text}</p>
          <a href="/profile/adam-radcliffe" className="mt-7 inline-flex items-center gap-2 border border-amber-400/50 bg-amber-400/10 px-4 py-3 text-sm font-semibold uppercase text-amber-200 transition hover:bg-amber-400/20">
            <FileSearch className="h-4 w-4" />
            Открыть досье
          </a>
        </Panel>

        <div className="grid gap-4 sm:grid-cols-2">
          {structure.council.map((item) => (
            <Panel key={item.title} seed={`council-${item.title}`}>
              <p className="kicker">совет</p>
              <h3 className="mt-3 font-display text-3xl uppercase text-stone-100">{item.title}</h3>
              <p className="mt-3 text-sm leading-6 text-stone-400">{item.text}</p>
            </Panel>
          ))}
        </div>
      </div>

      <Panel seed="structure-rear" className="mt-6">
        <p className="kicker">тыл</p>
        <h3 className="mt-3 font-display text-4xl uppercase text-stone-100">Люди, которые держат лагерь живым</h3>
        <div className="mt-6 flex flex-wrap gap-2">
          {structure.rear.map((item) => (
            <Tag key={item} tone="amber">{item}</Tag>
          ))}
        </div>
      </Panel>
    </section>
  )
}

function PeoplePage() {
  return (
    <section className="site-section">
      <SectionHeader
        eyebrow="личные дела"
        title="Люди Векселя"
        text="Каждое досье хранит роль, навыки, связи и историю внутри группы."
      />
      <div className="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
        {members.map((member) => (
          <CharacterCard key={member.slug} member={member} />
        ))}
      </div>
    </section>
  )
}

function ContractsPage() {
  return (
    <section className="site-section">
      <SectionHeader
        eyebrow="журнал оплаты"
        title="Контракты"
        text="Каждая задача получает номер, статус, оплату и ответственного клиента. Незаплаченная задача не выходит из лагеря."
      />
      <div className="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
        {contracts.map((contract) => (
          <ContractCard key={contract.number} contract={contract} />
        ))}
      </div>
    </section>
  )
}

function MapPage() {
  return (
    <section className="site-section">
      <SectionHeader
        eyebrow="региональная сетка"
        title="Карта региона"
        text="Базы, маршруты, зоны заражения и торговые точки, через которые проходит жизнь Векселя."
      />
      <RegionMap points={mapPoints} />
    </section>
  )
}

function GearPage() {
  return (
    <section className="site-section">
      <SectionHeader
        eyebrow="техника и снабжение"
        title="Техника"
        text="Машины, инструменты и радиосеть, без которых контракты остаются словами в журнале."
      />
      <div className="grid gap-5 md:grid-cols-2">
        {vehicles.map((group) => (
          <VehicleGroup key={group.category} group={group} />
        ))}
      </div>
    </section>
  )
}

function CodePage() {
  return (
    <section className="site-section">
      <SectionHeader
        eyebrow="внутренний порядок"
        title="Внутренний кодекс"
        text="Короткие правила, за нарушение которых не спорят у костра."
      />
      <div className="grid gap-4 md:grid-cols-2">
        {codeRules.map((rule, index) => (
          <Panel key={rule} seed={`code-${rule}`}>
            <div className="flex items-start gap-4">
              <span className="font-mono text-sm text-amber-300">{String(index + 1).padStart(2, '0')}</span>
              <p className="text-lg font-semibold text-stone-100">{rule}</p>
            </div>
          </Panel>
        ))}
      </div>
    </section>
  )
}

export default App
