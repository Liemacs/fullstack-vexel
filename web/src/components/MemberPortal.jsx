import { LogOut, Save, ShieldCheck } from 'lucide-react'
import { useEffect, useState } from 'react'
import { Panel, SectionHeader } from './Interface'

function skillLines(skills) {
  return skills.map((skill) => `${skill.name}|${skill.value}`).join('\n')
}

function listLines(items) {
  return items.join('\n')
}

function profileState(member) {
  return {
    name: member?.name ?? '',
    call_sign: member?.callSign ?? '',
    age: member?.age ?? '',
    status: member?.status ?? '',
    position_id: member?.position?.id ?? '',
    quote: member?.quote ?? '',
    bio: member?.bio ?? '',
    character: member?.character ?? '',
    vexel_history: member?.vexelHistory ?? '',
    skills_text: skillLines(member?.skills ?? []),
    gear_text: listLines(member?.gear ?? []),
    connections_text: listLines(member?.connections ?? []),
  }
}

export function MemberPortal({ authStatus, error, member, positions, onLogin, onLogout, onSave }) {
  const [credentials, setCredentials] = useState({ nickname: '', password: '' })
  const [profile, setProfile] = useState(() => profileState(member))
  const [image, setImage] = useState(null)
  const [saving, setSaving] = useState(false)

  useEffect(() => {
    setProfile(profileState(member))
    setImage(null)
  }, [member])

  async function submitLogin(event) {
    event.preventDefault()
    await onLogin(credentials)
  }

  async function submitProfile(event) {
    event.preventDefault()
    const payload = new FormData()

    Object.entries(profile).forEach(([key, value]) => {
      payload.set(key, value ?? '')
    })

    if (image) {
      payload.set('image', image)
    }

    setSaving(true)
    await onSave(payload)
    setSaving(false)
  }

  function updateProfile(key, value) {
    setProfile((current) => ({ ...current, [key]: value }))
  }

  if (authStatus === 'loading') {
    return (
      <section className="site-section">
        <Panel seed="member-portal-loading">
          <p className="font-lore text-sm leading-7 text-stone-500">Проверяем вход участника...</p>
        </Panel>
      </section>
    )
  }

  if (!member) {
    return (
      <section className="site-section">
        <SectionHeader
          eyebrow="личный кабинет"
          title="Вход участника"
          text="Используйте никнейм и пароль, выданные в dashboard, чтобы обновить данные своего героя."
        />
        <Panel seed="member-login">
          <form className="grid max-w-xl gap-5" onSubmit={submitLogin}>
            {error ? <p className="border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-200">{error}</p> : null}
            <label className="grid gap-2 text-sm font-semibold uppercase text-stone-400">
              Никнейм
              <input
                className="min-h-12 border border-stone-700 bg-stone-950 px-4 text-base text-stone-100 outline-none focus:border-amber-300"
                value={credentials.nickname}
                onChange={(event) => setCredentials((current) => ({ ...current, nickname: event.target.value }))}
                required
              />
            </label>
            <label className="grid gap-2 text-sm font-semibold uppercase text-stone-400">
              Пароль
              <input
                className="min-h-12 border border-stone-700 bg-stone-950 px-4 text-base text-stone-100 outline-none focus:border-amber-300"
                type="password"
                value={credentials.password}
                onChange={(event) => setCredentials((current) => ({ ...current, password: event.target.value }))}
                required
              />
            </label>
            <button className="primary-action w-fit" type="submit" disabled={authStatus === 'submitting'}>
              <ShieldCheck className="h-4 w-4" />
              {authStatus === 'submitting' ? 'Проверка...' : 'Войти'}
            </button>
          </form>
        </Panel>
      </section>
    )
  }

  return (
    <section className="site-section">
      <SectionHeader
        eyebrow="личный кабинет"
        title="Редактировать героя"
        text="Изменения сохраняются в базе данных и сразу попадают на публичную страницу профиля."
      />

      <Panel seed={`member-portal-${member.slug}`}>
        <div className="mb-6 flex flex-wrap items-center justify-between gap-4 border-b border-stone-800 pb-5">
          <div>
            <p className="text-sm uppercase text-amber-300">{member.nickname}</p>
            <h3 className="mt-2 font-display text-4xl uppercase text-stone-100">{member.name}</h3>
          </div>
          <button className="secondary-action" type="button" onClick={onLogout}>
            <LogOut className="h-4 w-4" />
            Выйти
          </button>
        </div>

        {error ? <p className="mb-5 border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-200">{error}</p> : null}

        <form className="grid gap-5" onSubmit={submitProfile}>
          <div className="grid gap-5 md:grid-cols-3">
            <label className="grid gap-2 text-sm font-semibold uppercase text-stone-400">
              Имя
              <input className="min-h-12 border border-stone-700 bg-stone-950 px-4 text-stone-100 outline-none focus:border-amber-300" value={profile.name} onChange={(event) => updateProfile('name', event.target.value)} required />
            </label>
            <label className="grid gap-2 text-sm font-semibold uppercase text-stone-400">
              Позывной
              <input className="min-h-12 border border-stone-700 bg-stone-950 px-4 text-stone-100 outline-none focus:border-amber-300" value={profile.call_sign} onChange={(event) => updateProfile('call_sign', event.target.value)} />
            </label>
            <label className="grid gap-2 text-sm font-semibold uppercase text-stone-400">
              Возраст
              <input className="min-h-12 border border-stone-700 bg-stone-950 px-4 text-stone-100 outline-none focus:border-amber-300" type="number" min="0" max="255" value={profile.age} onChange={(event) => updateProfile('age', event.target.value)} />
            </label>
          </div>

          <div className="grid gap-5 md:grid-cols-2">
            <label className="grid gap-2 text-sm font-semibold uppercase text-stone-400">
              Статус
              <input className="min-h-12 border border-stone-700 bg-stone-950 px-4 text-stone-100 outline-none focus:border-amber-300" value={profile.status} onChange={(event) => updateProfile('status', event.target.value)} />
            </label>
            <label className="grid gap-2 text-sm font-semibold uppercase text-stone-400">
              Должность
              <select className="min-h-12 border border-stone-700 bg-stone-950 px-4 text-stone-100 outline-none focus:border-amber-300" value={profile.position_id} onChange={(event) => updateProfile('position_id', event.target.value)}>
                <option value="">Без должности</option>
                {positions.map((position) => (
                  <option key={position.id} value={position.id}>{position.name}</option>
                ))}
              </select>
            </label>
          </div>

          <label className="grid gap-2 text-sm font-semibold uppercase text-stone-400">
            Цитата
            <textarea className="min-h-28 border border-stone-700 bg-stone-950 px-4 py-3 text-stone-100 outline-none focus:border-amber-300" value={profile.quote} onChange={(event) => updateProfile('quote', event.target.value)} />
          </label>
          <label className="grid gap-2 text-sm font-semibold uppercase text-stone-400">
            Биография
            <textarea className="min-h-36 border border-stone-700 bg-stone-950 px-4 py-3 text-stone-100 outline-none focus:border-amber-300" value={profile.bio} onChange={(event) => updateProfile('bio', event.target.value)} />
          </label>
          <div className="grid gap-5 md:grid-cols-2">
            <label className="grid gap-2 text-sm font-semibold uppercase text-stone-400">
              Характер
              <textarea className="min-h-32 border border-stone-700 bg-stone-950 px-4 py-3 text-stone-100 outline-none focus:border-amber-300" value={profile.character} onChange={(event) => updateProfile('character', event.target.value)} />
            </label>
            <label className="grid gap-2 text-sm font-semibold uppercase text-stone-400">
              История в Векселе
              <textarea className="min-h-32 border border-stone-700 bg-stone-950 px-4 py-3 text-stone-100 outline-none focus:border-amber-300" value={profile.vexel_history} onChange={(event) => updateProfile('vexel_history', event.target.value)} />
            </label>
          </div>
          <div className="grid gap-5 md:grid-cols-3">
            <label className="grid gap-2 text-sm font-semibold uppercase text-stone-400">
              Навыки
              <textarea className="min-h-32 border border-stone-700 bg-stone-950 px-4 py-3 text-stone-100 outline-none focus:border-amber-300" value={profile.skills_text} onChange={(event) => updateProfile('skills_text', event.target.value)} placeholder="Название|80" />
            </label>
            <label className="grid gap-2 text-sm font-semibold uppercase text-stone-400">
              Снаряжение
              <textarea className="min-h-32 border border-stone-700 bg-stone-950 px-4 py-3 text-stone-100 outline-none focus:border-amber-300" value={profile.gear_text} onChange={(event) => updateProfile('gear_text', event.target.value)} />
            </label>
            <label className="grid gap-2 text-sm font-semibold uppercase text-stone-400">
              Связи
              <textarea className="min-h-32 border border-stone-700 bg-stone-950 px-4 py-3 text-stone-100 outline-none focus:border-amber-300" value={profile.connections_text} onChange={(event) => updateProfile('connections_text', event.target.value)} />
            </label>
          </div>
          <label className="grid gap-2 text-sm font-semibold uppercase text-stone-400">
            Фото героя
            <input className="border border-stone-700 bg-stone-950 px-4 py-3 text-stone-100 file:mr-4 file:border-0 file:bg-amber-400 file:px-3 file:py-2 file:text-sm file:font-bold file:uppercase file:text-black" type="file" accept="image/*" onChange={(event) => setImage(event.target.files?.[0] ?? null)} />
          </label>

          <button className="primary-action w-fit" type="submit" disabled={saving}>
            <Save className="h-4 w-4" />
            {saving ? 'Сохранение...' : 'Сохранить'}
          </button>
        </form>
      </Panel>
    </section>
  )
}
