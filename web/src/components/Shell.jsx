import { Menu, X } from "lucide-react";
import { useState } from "react";
import { navItems } from "../data/vexelData";

function routeTo(id) {
  return id === "home" ? "/" : `/${id}`;
}

export function Shell({ activePage, children }) {
  const [isOpen, setIsOpen] = useState(false);

  return (
    <div className="min-h-screen bg-[#0d0d0d] text-stone-200">
      <div className="fixed inset-0 pointer-events-none z-0 bg-[radial-gradient(circle_at_20%_0%,rgba(216,160,43,0.16),transparent_32%),radial-gradient(circle_at_88%_18%,rgba(92,115,79,0.15),transparent_28%),linear-gradient(180deg,#111_0%,#090909_100%)]" />
      <div className="noise-layer" />
      <header className="fixed inset-x-0 top-0 z-50 border-b border-stone-800/80 bg-[#0d0d0d]/88 backdrop-blur-xl">
        <div className="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
          <a
            href="/"
            className="group flex items-center justify-center gap-3 flex-nowrap"
            onClick={() => setIsOpen(false)}
          >

              <svg
                viewBox="0 0 419 419"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
                className="w-8 h-8"
              >
                <g clipPath="url(#clip0_87_90)">
                  <rect x="99" y="127" width="220" height="35" fill="#D9D9D9" />
                  <rect x="99" y="192" width="220" height="35" fill="#D9D9D9" />
                  <rect x="99" y="257" width="220" height="35" fill="#D9D9D9" />
                  <path
                    d="M419 209.5C419 325.204 325.204 419 209.5 419C93.7963 419 0 325.204 0 209.5C0 93.7963 93.7963 0 209.5 0C325.204 0 419 93.7963 419 209.5ZM31.425 209.5C31.425 307.848 111.152 387.575 209.5 387.575C307.848 387.575 387.575 307.848 387.575 209.5C387.575 111.152 307.848 31.425 209.5 31.425C111.152 31.425 31.425 111.152 31.425 209.5Z"
                    fill="#D9D9D9"
                  />
                </g>
                <defs>
                  <clipPath id="clip0_87_90">
                    <rect width="419" height="419" fill="white" />
                  </clipPath>
                </defs>
              </svg>

            <span>
              <span className="block font-display text-xl uppercase leading-none text-stone-100">
                Вексель
              </span>
              <span className="block text-[10px] uppercase leading-none text-amber-300/80">
                internal archive
              </span>
            </span>
          </a>

          <nav className="hidden items-center gap-1 lg:flex">
            {navItems.map((item) => (
              <a
                key={item.id}
                href={routeTo(item.id)}
                className={`nav-link ${activePage === item.id ? "nav-link-active" : ""}`}
              >
                {item.label}
              </a>
            ))}
          </nav>

          <button
            type="button"
            className="grid h-10 w-10 place-items-center border border-stone-700 bg-stone-950 text-stone-200 lg:hidden"
            onClick={() => setIsOpen((value) => !value)}
            aria-label="Меню"
          >
            {isOpen ? <X className="h-5 w-5" /> : <Menu className="h-5 w-5" />}
          </button>
        </div>

        {isOpen ? (
          <nav className="grid gap-1 border-t border-stone-800 bg-[#101010] p-4 lg:hidden">
            {navItems.map((item) => (
              <a
                key={item.id}
                href={routeTo(item.id)}
                className={`nav-link ${activePage === item.id ? "nav-link-active" : ""}`}
                onClick={() => setIsOpen(false)}
              >
                {item.label}
              </a>
            ))}
          </nav>
        ) : null}
      </header>

      <main className="relative z-10 pt-16">{children}</main>

      <footer className="relative z-10 border-t border-stone-800 bg-[#0b0b0b] px-4 py-8 sm:px-6 lg:px-8">
        <div className="mx-auto flex max-w-7xl flex-col gap-3 text-sm text-stone-500 sm:flex-row sm:items-center sm:justify-between">
          <span>ВЕКСЕЛЬ / ВНУТРЕННИЙ АРХИВ / CHANNEL 13</span>
          <span>Задача должна быть оплачена.</span>
        </div>
      </footer>
    </div>
  );
}
