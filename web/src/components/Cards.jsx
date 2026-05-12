import { ArrowRight, UserRound } from 'lucide-react'
import { DataIcon, Panel, Tag } from './Interface'
import { cardWear } from '../utils/cardWear'

function statusTone(status) {
  if (status.includes('Провал')) return 'danger'
  if (status.includes('Актив')) return 'amber'
  if (status.includes('Контроль') || status.includes('Закрыта') || status.includes('Выполнено')) return 'green'
  return 'default'
}

export function CharacterCard({ member }) {
  const wear = cardWear(`dossier-${member.slug}`, 'group dossier-card')

  return (
    <a
      href={`/profile/${member.slug}`}
      className={wear.className}
      style={wear.style}
      data-serial={wear.serial}
    >
      <div className="relative aspect-[4/3] overflow-hidden bg-stone-900">
        <img
          src={member.image}
          alt={member.name}
          className="h-full w-full object-cover grayscale transition duration-700 group-hover:scale-105 group-hover:grayscale-0"
        />
        <div className="absolute inset-0 bg-gradient-to-t from-black via-black/25 to-transparent" />
        <Tag tone={member.status === 'Активен' || member.status === 'Активна' ? 'green' : 'amber'}>
          {member.status}
        </Tag>
      </div>
      <div className="p-5">
        <div className="flex items-start justify-between gap-3">
          <div>
            <h3 className="font-display text-3xl uppercase text-stone-100">{member.name}</h3>
            <p className="text-sm uppercase text-amber-300">{member.callSign}</p>
          </div>
          <UserRound className="mt-1 h-5 w-5 text-stone-500 transition group-hover:text-amber-300" />
        </div>
        <p className="mt-4 font-lore text-sm text-stone-400">{member.role}</p>
        <p className="mt-2 font-lore text-sm text-stone-500">{member.specialization}</p>
        <span className="mt-5 inline-flex items-center gap-2 text-sm font-semibold uppercase text-amber-300">
          Досье <ArrowRight className="h-4 w-4 transition group-hover:translate-x-1" />
        </span>
      </div>
    </a>
  )
}

export function ContractCard({ contract }) {
  return (
    <Panel seed={`contract-${contract.number}`} className="flex h-full flex-col gap-5">
      <div className="flex items-start justify-between gap-3">
        <div>
          <p className="font-mono text-sm text-amber-300">{contract.number}</p>
          <h3 className="mt-2 font-display text-3xl uppercase text-stone-100">{contract.title}</h3>
        </div>
      </div>
      <p className="font-lore text-sm leading-6 text-stone-400">{contract.text}</p>
      <div className="mt-auto grid gap-3 text-sm text-stone-400">
        <div className="flex items-center justify-between border-t border-stone-800 pt-3">
          <span>Статус</span>
          <Tag tone={statusTone(contract.status)}>{contract.status}</Tag>
        </div>
        <div className="flex items-center justify-between">
          <span>Оплата</span>
          <span className="text-stone-200">{contract.payment}</span>
        </div>
        <div className="flex items-center justify-between">
          <span>Клиент</span>
          <span className="text-stone-200">{contract.client}</span>
        </div>
      </div>
    </Panel>
  )
}

export function VehicleGroup({ group }) {
  return (
    <Panel seed={`vehicle-${group.category}`}>
      <div className="mb-5 flex items-center gap-3">
        <span className="grid h-10 w-10 place-items-center border border-amber-400/30 bg-amber-400/10 text-amber-200">
          <DataIcon name={group.icon} />
        </span>
        <h3 className="font-display text-3xl uppercase text-stone-100">{group.category}</h3>
      </div>
      <div className="grid gap-4">
        {group.items.map((item) => (
          <div key={item.name} className="border-l-2 border-stone-800 pl-4">
            <div className="flex flex-wrap items-center justify-between gap-2">
              <h4 className="font-semibold text-stone-100">{item.name}</h4>
              <Tag tone={item.status === 'готов' || item.status === 'на ходу' || item.status === 'активно' ? 'green' : 'amber'}>
                {item.status}
              </Tag>
            </div>
            <p className="mt-2 font-lore text-sm leading-6 text-stone-400">{item.text}</p>
          </div>
        ))}
      </div>
    </Panel>
  )
}
