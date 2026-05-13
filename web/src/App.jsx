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
import { MemberPortal } from './components/MemberPortal'
import { RegionMap } from './components/RegionMap'
import { Shell } from './components/Shell'
import { cardWear } from './utils/cardWear'
import {
  codeRules,
  anthemAudio,
  leadershipCards,
  mapPoints,
  navItems,
  placeholderImage,
  principles,
  services,
  structure,
  terminalLines,
  vehicles,
} from './data/vexelData'

const API_BASE_URL = window.__VEXEL_CONFIG__?.API_BASE_URL ?? import.meta.env.VITE_API_BASE_URL ?? 'http://127.0.0.1:8000/api/v1'
const API_ORIGIN = new URL(API_BASE_URL, window.location.origin).origin
const MEMBER_TOKEN_KEY = 'vexel_member_token'
const UNKNOWN_MEMBER_IMAGE = '/images/members/unknown.webp'

function storedMemberToken() {
  try {
    return window.localStorage.getItem(MEMBER_TOKEN_KEY)
  } catch {
    return null
  }
}

function backendAssetUrl(value, fallback = placeholderImage) {
  if (!value) {
    return fallback
  }

  if (/^https?:\/\//.test(value)) {
    return value
  }

  if (value.startsWith('/uploads/')) {
    return `${API_ORIGIN}${value}`
  }

  return value
}

function normalizeMember(member) {
  const position = member.position
    ? {
        ...member.position,
        image: backendAssetUrl(member.position.image),
      }
    : null

  return {
    slug: member.slug,
    nickname: member.nickname ?? '',
    name: member.name ?? '',
    callSign: member.callSign ?? '',
    age: member.age ?? '',
    status: member.status ?? '',
    role: member.role ?? member.position?.name ?? '',
    position,
    specialization: member.specialization ?? '',
    image: backendAssetUrl(member.image, UNKNOWN_MEMBER_IMAGE),
    quote: member.quote ?? '',
    bio: member.bio ?? '',
    skills: Array.isArray(member.skills) ? member.skills : [],
    gear: Array.isArray(member.gear) ? member.gear : [],
    character: member.character ?? '',
    vexelHistory: member.vexelHistory ?? '',
    connections: Array.isArray(member.connections) ? member.connections : [],
  }
}

function normalizePosition(position) {
  return {
    ...position,
    image: backendAssetUrl(position.image),
  }
}

function authHeaders(authToken) {
  return authToken ? { Authorization: `Bearer ${authToken}` } : {}
}

function useApiMembers(authToken) {
  const [members, setMembers] = useState([])
  const [status, setStatus] = useState('loading')

  useEffect(() => {
    let isMounted = true

    fetch(`${API_BASE_URL}/members`, {
      headers: authHeaders(authToken),
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error('Members request failed')
        }

        return response.json()
      })
      .then((data) => {
        if (!isMounted) {
          return
        }

        setMembers(Array.isArray(data) ? data.map(normalizeMember) : [])
        setStatus('ready')
      })
      .catch(() => {
        if (!isMounted) {
          return
        }

        setMembers([])
        setStatus('error')
      })

    return () => {
      isMounted = false
    }
  }, [authToken])

  return { members, status, setMembers }
}

function usePositions(authToken) {
  const [positions, setPositions] = useState([])

  useEffect(() => {
    if (!authToken) {
      setPositions([])
      return undefined
    }

    let isMounted = true

    fetch(`${API_BASE_URL}/positions`, {
      headers: authHeaders(authToken),
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error('Positions request failed')
        }

        return response.json()
      })
      .then((data) => {
        if (isMounted) {
          setPositions(Array.isArray(data) ? data.map(normalizePosition) : [])
        }
      })
      .catch(() => {
        if (isMounted) {
          setPositions([])
        }
      })

    return () => {
      isMounted = false
    }
  }, [authToken])

  return positions
}

function useApiContracts(authToken) {
  const [contracts, setContracts] = useState([])
  const [status, setStatus] = useState(authToken ? 'loading' : 'locked')

  useEffect(() => {
    if (!authToken) {
      setContracts([])
      setStatus('locked')
      return undefined
    }

    let isMounted = true
    setStatus('loading')

    fetch(`${API_BASE_URL}/contracts`, {
      headers: authHeaders(authToken),
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error('Contracts request failed')
        }

        return response.json()
      })
      .then((data) => {
        if (!isMounted) {
          return
        }

        setContracts(Array.isArray(data) ? data : [])
        setStatus('ready')
      })
      .catch(() => {
        if (!isMounted) {
          return
        }

        setContracts([])
        setStatus('error')
      })

    return () => {
      isMounted = false
    }
  }, [authToken])

  return { contracts, status }
}

function useOverview() {
  const [overview, setOverview] = useState({
    members: 0,
    activeContracts: 0,
  })

  useEffect(() => {
    let isMounted = true

    fetch(`${API_BASE_URL}/overview`)
      .then((response) => {
        if (!response.ok) {
          throw new Error('Overview request failed')
        }

        return response.json()
      })
      .then((data) => {
        if (!isMounted) {
          return
        }

        setOverview({
          members: Number(data.members ?? 0),
          activeContracts: Number(data.active_contracts ?? 0),
        })
      })
      .catch(() => {
        if (isMounted) {
          setOverview({ members: 0, activeContracts: 0 })
        }
      })

    return () => {
      isMounted = false
    }
  }, [])

  return overview
}

function upsertMember(members, member) {
  const exists = members.some((item) => item.nickname === member.nickname)

  if (!exists) {
    return [...members, member]
  }

  return members.map((item) => (item.nickname === member.nickname ? member : item))
}

async function apiMessage(response) {
  try {
    const data = await response.json()
    return data.message ?? 'Не удалось выполнить запрос.'
  } catch {
    return 'Не удалось выполнить запрос.'
  }
}

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
  const [authToken, setAuthToken] = useState(storedMemberToken)
  const [currentMember, setCurrentMember] = useState(null)
  const [authStatus, setAuthStatus] = useState(authToken ? 'loading' : 'guest')
  const [authError, setAuthError] = useState('')
  const isMemberLoggedIn = authStatus === 'authenticated' && Boolean(currentMember)
  const { members, status: membersStatus, setMembers } = useApiMembers(authToken)
  const { contracts, status: contractsStatus } = useApiContracts(authToken)
  const positions = usePositions(authToken)
  const overview = useOverview()

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

  useEffect(() => {
    if (!authToken) {
      setCurrentMember(null)
      setAuthStatus('guest')
      return undefined
    }

    let isMounted = true
    setAuthStatus('loading')
    setAuthError('')

    fetch(`${API_BASE_URL}/member/me`, {
      headers: { Authorization: `Bearer ${authToken}` },
    })
      .then(async (response) => {
        if (!response.ok) {
          throw new Error(await apiMessage(response))
        }

        return response.json()
      })
      .then((data) => {
        if (!isMounted) {
          return
        }

        const member = normalizeMember(data.member)
        setCurrentMember(member)
        setMembers((current) => upsertMember(current, member))
        setAuthStatus('authenticated')
      })
      .catch((error) => {
        if (!isMounted) {
          return
        }

        window.localStorage.removeItem(MEMBER_TOKEN_KEY)
        setAuthToken(null)
        setCurrentMember(null)
        setAuthError(error.message)
        setAuthStatus('guest')
      })

    return () => {
      isMounted = false
    }
  }, [authToken, setMembers])

  async function handleMemberLogin(credentials) {
    setAuthStatus('submitting')
    setAuthError('')

    try {
      const response = await fetch(`${API_BASE_URL}/member/login`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(credentials),
      })

      if (!response.ok) {
        throw new Error(await apiMessage(response))
      }

      const data = await response.json()
      const member = normalizeMember(data.member)

      window.localStorage.setItem(MEMBER_TOKEN_KEY, data.token)
      setAuthToken(data.token)
      setCurrentMember(member)
      setMembers((current) => upsertMember(current, member))
      setAuthStatus('authenticated')
    } catch (error) {
      setAuthError(error.message)
      setAuthStatus('guest')
    }
  }

  async function handleMemberSave(payload) {
    if (!authToken) {
      setAuthError('Требуется вход участника.')
      return
    }

    setAuthError('')

    try {
      const response = await fetch(`${API_BASE_URL}/member/me`, {
        method: 'POST',
        headers: { Authorization: `Bearer ${authToken}` },
        body: payload,
      })

      if (!response.ok) {
        throw new Error(await apiMessage(response))
      }

      const data = await response.json()
      const member = normalizeMember(data.member)

      setCurrentMember(member)
      setMembers((current) => upsertMember(current, member))
    } catch (error) {
      setAuthError(error.message)
    }
  }

  async function handleMemberLogout() {
    if (authToken) {
      await fetch(`${API_BASE_URL}/member/logout`, {
        method: 'POST',
        headers: { Authorization: `Bearer ${authToken}` },
      }).catch(() => {})
    }

    window.localStorage.removeItem(MEMBER_TOKEN_KEY)
    setAuthToken(null)
    setCurrentMember(null)
    setAuthError('')
    setAuthStatus('guest')
  }

  const activePage = route.page === 'profile' ? 'people' : route.page
  const profile = route.page === 'profile' ? members.find((member) => member.slug === route.slug) : null

  return (
    <Shell activePage={activePage}>
      {route.page === 'profile' ? (
        <ProfilePage profile={profile} status={membersStatus} isMemberLoggedIn={isMemberLoggedIn} />
      ) : (
        <Page
          page={route.page}
          members={members}
          membersStatus={membersStatus}
          contracts={contracts}
          contractsStatus={contractsStatus}
          positions={positions}
          overview={overview}
          authStatus={authStatus}
          authError={authError}
          currentMember={currentMember}
          isMemberLoggedIn={isMemberLoggedIn}
          onMemberLogin={handleMemberLogin}
          onMemberLogout={handleMemberLogout}
          onMemberSave={handleMemberSave}
        />
      )}
    </Shell>
  )
}

function Page({
  page,
  members,
  membersStatus,
  contracts,
  contractsStatus,
  positions,
  overview,
  authStatus,
  authError,
  currentMember,
  isMemberLoggedIn,
  onMemberLogin,
  onMemberLogout,
  onMemberSave,
}) {
  switch (page) {
    case 'history':
      return <HistoryPage />
    case 'principles':
      return <PrinciplesPage />
    case 'structure':
      return <StructurePage />
    case 'people':
      return <PeoplePage members={members} status={membersStatus} isMemberLoggedIn={isMemberLoggedIn} />
    case 'login':
      return (
        <MemberPortal
          authStatus={authStatus}
          error={authError}
          member={currentMember}
          positions={positions}
          onLogin={onMemberLogin}
          onLogout={onMemberLogout}
          onSave={onMemberSave}
        />
      )
    case 'contracts':
      return <ContractsPage contracts={contracts} status={contractsStatus} isMemberLoggedIn={isMemberLoggedIn} />
    case 'map':
      return <MapPage />
    case 'gear':
      return <GearPage />
    case 'code':
      return <CodePage />
    case 'home':
    default:
      return <HomePage overview={overview} />
  }
}

function ProfilePage({ profile, status, isMemberLoggedIn }) {
  if (profile) {
    return <CharacterProfile member={profile} showSensitive={isMemberLoggedIn} />
  }

  return (
    <section className="site-section">
      <Panel seed="profile-empty">
        <p className="kicker">database</p>
        <h2 className="mt-3 font-display text-5xl uppercase text-stone-100">
          {status === 'loading' ? 'Загрузка досье' : 'Досье не найдено'}
        </h2>
        <p className="mt-4 font-lore text-base leading-8 text-stone-400">
          {status === 'error'
            ? 'Не удалось получить данные участников из backend. Проверьте, что Laravel API запущен.'
            : 'Участник появится здесь после добавления в dashboard.'}
        </p>
      </Panel>
    </section>
  )
}

function AccessLockedPanel({ title = 'Доступ закрыт' }) {
  return (
    <Panel seed={`locked-${title}`}>
      <p className="kicker">требуется вход</p>
      <h2 className="mt-3 font-display text-5xl uppercase text-stone-100">{title}</h2>
      <p className="mt-4 max-w-2xl font-lore text-base leading-8 text-stone-400">
        Эти данные доступны только участникам Векселя после входа в кабинет.
      </p>
      <a href="/login" className="mt-6 inline-flex w-fit border border-amber-400/50 bg-amber-400/10 px-4 py-3 text-sm font-semibold uppercase text-amber-200 transition hover:bg-amber-400/20">
        Войти в кабинет
      </a>
    </Panel>
  )
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

function HomePage({ overview }) {
  const stats = [
    ['99.7', 'канал'],
    [String(overview.activeContracts).padStart(2, '0'), 'контрактов'],
    [String(overview.members).padStart(2, '0'), 'досье'],
  ]

  return (
    <>
      <section className="hero-section">
        <img src={placeholderImage} alt="" className="absolute inset-0 h-full w-full object-cover" />
        <div className="absolute inset-0 bg-gradient-to-r from-black via-black/75 to-black/20" />
        <div className="absolute inset-0 bg-[linear-gradient(90deg,rgba(216,160,43,0.12)_1px,transparent_1px),linear-gradient(0deg,rgba(255,255,255,0.035)_1px,transparent_1px)] bg-[size:96px_96px]" />

        <div className="relative mx-auto grid min-h-[86svh] max-w-7xl items-end gap-10 px-4 pb-10 pt-16 sm:px-6 lg:grid-cols-[1.1fr_0.75fr] lg:px-8">
          <div className="pb-8">
            <p className="kicker">внутренняя база / channel 99.7</p>
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
              {stats.map(([value, label]) => (
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
  const [timelineEntries, setTimelineEntries] = useState([])
  const [selectedYear, setSelectedYear] = useState(null)
  const [status, setStatus] = useState('loading')

  useEffect(() => {
    let isMounted = true

    fetch(`${API_BASE_URL}/timeline`)
      .then((response) => {
        if (!response.ok) {
          throw new Error('Timeline request failed')
        }

        return response.json()
      })
      .then((data) => {
        if (!isMounted) {
          return
        }

        const entries = Array.isArray(data)
          ? data.map((entry) => ({
              ...entry,
              chapters: Array.isArray(entry.chapters) ? entry.chapters : [],
            }))
          : []
        setTimelineEntries(entries)
        setSelectedYear((currentYear) => {
          if (entries.some((entry) => entry.year === currentYear)) {
            return currentYear
          }

          return entries[0]?.year ?? null
        })
        setStatus('ready')
      })
      .catch(() => {
        if (!isMounted) {
          return
        }

        setTimelineEntries([])
        setSelectedYear(null)
        setStatus('error')
      })

    return () => {
      isMounted = false
    }
  }, [])

  const selectedEntry = timelineEntries.find((item) => item.year === selectedYear) ?? timelineEntries[0] ?? null

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
          {status === 'loading' ? (
            <p className="mt-6 font-lore text-sm leading-7 text-stone-500">Загрузка данных из архива...</p>
          ) : null}
          {status === 'error' ? (
            <p className="mt-6 font-lore text-sm leading-7 text-red-300">
              Не удалось получить данные из backend. Проверьте, что Laravel запущен на 127.0.0.1:8000.
            </p>
          ) : null}
          {status === 'ready' && timelineEntries.length === 0 ? (
            <p className="mt-6 font-lore text-sm leading-7 text-stone-500">
              В базе данных пока нет событий хронологии. Добавьте первое событие в dashboard.
            </p>
          ) : null}
          <div className="mt-6 grid gap-5">
            {timelineEntries.map((item) => (
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

        {selectedEntry ? (
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
        ) : (
          <Panel seed="history-empty">
            <p className="kicker">database</p>
            <h3 className="mt-3 font-display text-5xl uppercase text-stone-100">Архив пуст</h3>
            <p className="mt-4 font-lore text-base leading-8 text-stone-400">
              Создайте событие в backend dashboard, и оно появится на этой странице автоматически.
            </p>
          </Panel>
        )}
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

function PeoplePage({ members, status, isMemberLoggedIn }) {
  return (
    <section className="site-section">
      <SectionHeader
        eyebrow="личные дела"
        title="Люди Векселя"
        text="Каждое досье хранит роль, навыки, связи и историю внутри группы."
      />
      {status === 'loading' ? (
        <Panel seed="people-loading">
          <p className="font-lore text-sm leading-7 text-stone-500">Загрузка участников из базы данных...</p>
        </Panel>
      ) : null}
      {status === 'error' ? (
        <Panel seed="people-error">
          <p className="font-lore text-sm leading-7 text-red-300">
            Не удалось получить участников из backend. Проверьте, что Laravel API запущен.
          </p>
        </Panel>
      ) : null}
      {status === 'ready' && members.length === 0 ? (
        <Panel seed="people-empty">
          <p className="kicker">database</p>
          <h3 className="mt-3 font-display text-5xl uppercase text-stone-100">Участников пока нет</h3>
          <p className="mt-4 font-lore text-base leading-8 text-stone-400">
            Создайте участника в dashboard, и он появится на этой странице автоматически.
          </p>
        </Panel>
      ) : null}
      {members.length > 0 ? (
        <div className="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
          {members.map((member) => (
            <CharacterCard key={member.slug} member={member} showSensitive={isMemberLoggedIn} />
          ))}
        </div>
      ) : null}
    </section>
  )
}

function ContractsPage({ contracts, status, isMemberLoggedIn }) {
  if (!isMemberLoggedIn) {
    return (
      <section className="site-section">
        <SectionHeader
          eyebrow="журнал оплаты"
          title="Контракты"
          text="Контрактный журнал доступен только участникам Векселя."
        />
        <AccessLockedPanel title="Контракты скрыты" />
      </section>
    )
  }

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
      {status === 'ready' && contracts.length === 0 ? (
        <Panel seed="contracts-empty" className="mt-6">
          <p className="font-lore text-stone-400">Контрактов пока нет.</p>
        </Panel>
      ) : null}
      {status === 'error' ? (
        <Panel seed="contracts-error" className="mt-6">
          <p className="font-lore text-stone-400">Не удалось загрузить контракты.</p>
        </Panel>
      ) : null}
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
        title={codeRules.title}
        text={codeRules.intro}
      />
      <div className="grid gap-5 lg:grid-cols-2">
        {codeRules.sections.map((section) => (
          <Panel key={section.title} seed={`code-${section.title}`} className="flex h-full flex-col gap-5">
            <h3 className="font-display text-3xl uppercase text-stone-100">{section.title}</h3>
            <div className="grid gap-4">
              {section.rules.map((rule, index) => (
                <div key={rule} className="flex items-start gap-4 border-l border-amber-400/30 pl-4">
                  <span className="font-mono text-sm text-amber-300">{String(index + 1).padStart(2, '0')}</span>
                  <p className="font-lore text-sm leading-6 text-stone-300">{rule}</p>
                </div>
              ))}
            </div>
          </Panel>
        ))}
      </div>

      <Panel seed="punishments" className="mt-6">
        <div className="mb-6">
          <p className="font-mono text-sm uppercase text-amber-300">Наказания</p>
          <p className="mt-3 font-lore text-sm leading-6 text-stone-400">{codeRules.punishmentsIntro}</p>
        </div>
        <div className="grid gap-5">
          {codeRules.punishments.map((item) => (
            <div key={item.level} className="border-t border-stone-800 pt-5">
              <div className="grid gap-4 lg:grid-cols-[180px_1fr_260px]">
                <div>
                  <p className="font-display text-3xl uppercase text-stone-100">{item.level}</p>
                </div>
                <div>
                  <p className="mb-3 font-mono text-xs uppercase text-stone-500">Деяние</p>
                  <ul className="grid gap-2 font-lore text-sm leading-6 text-stone-300">
                    {item.deeds.map((deed) => (
                      <li key={deed}>— {deed}</li>
                    ))}
                  </ul>
                </div>
                <div>
                  <p className="mb-3 font-mono text-xs uppercase text-stone-500">Наказание</p>
                  <p className="font-semibold text-amber-200">{item.punishment}</p>
                  <p className="mt-3 font-lore text-sm leading-6 text-stone-400">{item.text}</p>
                </div>
              </div>
            </div>
          ))}
        </div>
      </Panel>

      <Panel seed="calculation" className="mt-6">
        <p className="font-mono text-sm uppercase text-amber-300">Расчёт</p>
        <div className="mt-5 grid gap-4">
          {codeRules.calculation.map((rule, index) => (
            <div key={rule} className="flex items-start gap-4">
              <span className="font-mono text-sm text-amber-300">{String(index + 1).padStart(2, '0')}</span>
              <p className="font-lore text-sm leading-6 text-stone-300">{rule}</p>
            </div>
          ))}
        </div>
      </Panel>
    </section>
  )
}

export default App
