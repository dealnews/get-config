# Contributing

Thank you for considering contributing to this project. We welcome all changes
through merge requests, and will work with you to make sure that what you are
trying to accomplish will fit into the overall project's vision and scope.
<br />
<br />
If you aren't sure if your changes are appropriate, or what might be the best
way to solve the problem you are having, please discuss the change you wish to
make with a project team member, through Slack or in person, and we can help
you get started in the right direction.

## Merge Request Process

1. Discuss your proposed changes with project team members if necessary.
2. Ensure that your changes meet the coding style of the project, appropriate
   tests are written, and that all tests pass.
3. Push your changes to a feature branch, and create a new merge request.
4. Ask for a code review and merge of your changes.

## Rules of the Road

Respect the coding style and workflow of the project. One projects coding
style, workflow and processes aren't necessarily the same as another project.
Please be respectful of other teams and treat other projects as you would your
own.

### Use a proper Git config.

Make sure that you've [setup
Git](http://git-scm.com/book/en/Getting-Started-First-Time-Git-Setup), and that
you've set your full name and company email address in your `~/.gitconfig` so
that your contributions are attributed properly.

```
git config --global user.name "Joe Developer"
git config --global user.email jdeveloper@example.com
```

### Use a Feature Branch

All repositories use a *feature branch* workflow. Except for select
repositories, the `master` branch represents the current released version, and
all future development is done in branches, typically created for each
different feature being worked on. A comprehensive explanation of the feature
branch workflow can be found
[here](https://www.atlassian.com/git/tutorials/comparing-workflows#feature-branch-workflow).
<br />
<br />
Feature branch names should be descriptive and concise with words separated by
underscores, and should always refer to a issue number if one exists.
<br />
<br />
**Good:**
```
git checkout -b issue_1234_mysql_cluster_fix
git checkout -b aggregation_cron_jobs
```

**Bad:**
```
git checkout -b redmine1234
git checkout -b cron_stuff
git checkout -b fixed_error
```

### Commit Messages

It's very important that commit messages for contributions to a repository are
useful and well written with proper capitalization, spelling, and proper use of
the English language. Commit messages should use descriptive comments with
issue references where applicable.

#### Formatting

Try to keep the first line to between 50 and 72 characters in length.  The
first line, called the *summary*, of a commit message should be concise, but as
descriptive as possible. Additional details, wrapped at 72 characters, are
encouraged and *must* follow a blank line after the summary text.
<br />
<br />
Of course, in the real world, there are always exceptions and situations
where this may not be ideal. Just one more word might be the difference
between a good summary and a bad summary and this rule should never keep a
developer from doing the best thing for the situation.

Example:

```
Capitalized, short (72 chars or less) summary

More detailed explanatory text, if necessary. Wrap it to about 72
characters or so. The blank line separating the summary from the body
is critical (unless you omit the body entirely); tools like rebase can
get confused if you run the two together.

Further paragraphs come after blank lines.

Fixes #1234
```

#### Reference Related Issues

Always reference related issues if one exists. Any issue references should be placed at the end of the commit message separated by a newline.

#### Don't Be Lazy

Commit messages must **never** only say *Fixed bug*, or *Fixed #1234*, but be
as descriptive as possible and answer questions such as:

* What does the change do?
* What is the reason for the change?
* What effects does this change have that other developers should know about?

#### Resources

Two great resources on crafting well written and useful commit messages:

* http://tbaggery.com/2008/04/19/a-note-about-git-commit-messages.html
* http://robots.thoughtbot.com/5-useful-tips-for-a-better-commit-message

### Writing Tests

Please add or update tests that cover the changes being made. Various projects
have different levels of test coverage, and tests may be a combination of unit,
functional, or other types. Tests are generally simple and declarative in
nature and properly written tests work to ensure that future development is
consistent and generally as bug free as possible.
<br />
<br />
If you aren't familiar with a testing language, or test structure, ask for
help. Project team members will work with you to write appropriate tests.
