# Git

General notes can be found in the Best Practices document:
https://silverstripe.atlassian.net/wiki/spaces/DEV/pages/186384851/Developer+Best+Practices#DeveloperBestPractices-VersionControlandPeerReview

Did you know you can integrate Jira with your project repo? You can read more about it here:
https://silverstripe.atlassian.net/wiki/spaces/DEV/pages/820510952/How+to+integrate+your+git+repository+and+your+Jira+board

(These guidelines are useful in any case, but are targeted towards providing the best Jira integration support)

## Branch names

One useful feature when you have Jira integration is that when you tag a story or subtask with a "version" tag, if you
have referenced that story or subtask's ticket number in the pull request or your branch name, then the status of that
pull request will show up in your "releases" report.

If, as part of your project, you have decided that version tags are applied to your stories, then the easiest way
to make sure all of your pull requests get linked correctly, is to use the story ticket number as part of your branch
name.

EG, my story number is SS-123, and the story is requesting a dev task to populate default records. My branch might be
called:
`feature/SS-123-populate-defaults-dev-task`

Note: If, say, your story has the version tag, but you only ever reference subtasks in your branch name and pull
request, then the link to the story will not be created, and so the pull request will not show up in the releases
report.

## Commit messages

For any commit related to a story or subtask, you should include either the story or subtask ticket number at the start
of your commit message. EG:
TNZ-111: A short description of the change set

Keep in mind that any ticket number you include in your commit title **or message** will get linked to in Jira - and
the goal should be to have these links anywhere/everywhere they might be relevant.

Further suggestions for commit messages can be found in the
[commit message template](../.github/COMMIT_MESSAGE_TEMPLATE.md).

## Commit message template

There is a commit message template provided which can be optionally used. These are suggestions, not rules.

**Why the 72 char limit?**

Github automatically truncates your commit title down to 72 characters and hides the rest, so, you should (ideally)
write succinct enough commit titles that folks can read it in it's entirety without having the click "..." to see
the rest.

**Why the 50 char suggestion then?**

More succinct is better than less succinct. The goal is set purposefully short so that you're more likely to think about
that succinct title aiming to be under 50, which at the very least, means you're *more* likely to be under 72.

### Using the commit message template

From your project root directory, run the following:
```
$ git config --local commit.template .github/COMMIT_MESSAGE_TEMPLATE.md
```

This will set the template only for this project.

It's important to note that the template will only show for you if you use `git commit`, and don't use either of:

* `git commit -m ""` or
* `git commit --message ""`
