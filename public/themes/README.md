# EduCMS Themes

Foundation directory structure for RC5-010 (Theme Website). Each subfolder
corresponds to a template entry managed at **Admin > Theme Website**
(`settings.theme.active_theme`).

RC5 scope: only `default/` is a working template. `modern/`, `corporate/`,
and `islamic/` are reserved placeholders ("Coming Soon" in the admin UI) —
selecting them is rejected server-side in `Theme_website::save()` until
template files actually exist here.

No theme builder, color picker, or CSS generator is provided by design:
each theme owns its own styling.
