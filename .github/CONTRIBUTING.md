# Contributing

Contributions are **welcome** and will be fully **credited**.

Please read and understand the contribution guide before creating an issue or pull request.

## Etiquette

This project is open source, and as such, the maintainers give their free time to build and maintain the source code
held within. They make the code freely available in the hope that it will be of use to other developers. It would be
extremely unfair for them to suffer abuse or anger for their hard work.

Please be considerate towards maintainers when raising issues or presenting pull requests. Let's show the
world that developers are civilized and selfless people.

It's the duty of the maintainer to ensure that all submissions to the project are of enough
quality to benefit the project. Many developers have different skills, strengths, and weaknesses. Respect the maintainer's decision, and do not be upset or abusive if your submission is not used.

## Viability

When requesting or submitting new features, first consider whether it might be useful to others. Open
source projects are used by many developers, who may have entirely different needs to your own. Think about
whether your feature is likely to be used by other users of the project.

## Procedure

Before filing an issue:

- Attempt to replicate the problem, to ensure that it wasn't a coincidental incident.
- Check to make sure your feature suggestion isn't already present within the project.
- Check the pull requests tab to ensure that the bug doesn't have a fix in progress.
- Check the pull requests tab to ensure that the feature isn't already in progress.

Before submitting a pull request:

- Check the codebase to ensure that your feature doesn't already exist.
- Check the pull requests to ensure that another person hasn't already submitted the feature or fix.

## Development

The repository contains a Workbench — a small Laravel application, powered by
Orchestra Testbench, that consumes the package the way a real application would. You do
not need a separate Laravel project, a database server or a Node toolchain to work on the package.

Install dependencies:

```bash
composer install
```

Build the Workbench database and seed it:

```bash
composer build
```

Start the Workbench application:

```bash
composer serve
```

`composer serve` builds first, so a fresh clone needs nothing else. The Workbench serves on
`http://127.0.0.1:8000` and requires no authentication:

| URL | What it exercises |
|---|---|
| `/` | The list of seeded posts. |
| `/posts/{slug}` | Markdown, HTML and rich editor output side by side, each rendered and as source. |
| `/posts/undecorated-example` | The same presets with inline decorations disabled. |

The Workbench seeds four `Workbench\App\Models\Post` records whose `markdown_content`,
`html_content` and `rich_content` columns are filled by `Workbench\Database\Factories\PostFactory`
using the three fakers, so changes to a faker are visible immediately.

Workbench code lives in `workbench/` and represents the *consuming application*.
Nothing in `workbench/` ships to consumers.

## Testing

Run the full suite — Rector (dry run), Pint (check only), Larastan and Pest:

```bash
composer test
```

Each step can also be run on its own:

```bash
composer test:refactor  # preview Rector changes
composer test:lint      # check formatting without changing files
composer test:types     # run Larastan static analysis
composer test:unit      # run the Pest test suite
composer test:coverage  # run Pest with code coverage
```

To apply fixes rather than check for them:

```bash
composer lint       # apply Pint formatting
composer refactor   # apply Rector refactorings
```

## Requirements

If the project maintainer has any additional requirements, you will find them listed here.

- **Follow the code style** – The package is formatted with [Laravel Pint](https://laravel.com/docs/pint), refactored with [Rector](https://getrector.com) and analysed with [Larastan](https://github.com/larastan/larastan). `composer test` checks all three.

- **Add tests!** — Your patch won't be accepted if it doesn't have tests.

- **Document any change in behavior** – Make sure the `README.md` and the documentation in `docs/` are kept up to date.

- **Consider our release cycle** – We try to follow [SemVer v2.0.0](https://semver.org/). Randomly breaking public APIs is not an option.

- **One pull request per feature** – If you want to do more than one thing, send multiple pull requests.

- **Send coherent history** – Make sure each commit in your pull request is meaningful. If you had to make multiple intermediate commits while developing, please [squash them](https://www.git-scm.com/book/en/v2/Git-Tools-Rewriting-History#Changing-Multiple-Commit-Messages) before submitting.

**Happy coding**!
