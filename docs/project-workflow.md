# Project workflow

Each team is going to be slightly different. Below is a simple workflow that you might see while working on a project
at Silverstripe. You should update these docs to match the workflow for your project.

The workflows described below match the Swimlanes used on the scrum/kanban board for the project (with the exception of
"Task huddle", which happens before work is kicked off).

TL;DR: https://go.gliffy.com/go/publish/13322403

## Task Huddle

Some teams perform task huddles as part of sprint planning, others perform it as the "first step" for a new sprint. In
either case:

The team should group up to talk through the story. The goal of this "huddle" is to come to a high level consensus on
how the work for this story will be delivered. This might include discussing some of the high level technical
requirements.

Subtasks should be created to represent the work that needs to be completed in order for this story to meet your
[Definition of Done](definition-of-ready-done.md#definition-of-done).

The team (as a whole) should leave the huddle with a reasonable understanding of how the work is going to be completed.

## To do

Any subtasks that are not flagged or already assigned to another user can be picked up for work to begin.

## In progress

Someone has been assigned to a subtask, and work is currently underway.

Before the "In progress" step is complete, the assignee should create/update any relevant documentation. This could
include updating subtask descriptions/comments, or updating Confluence docs, etc.

### For tasks that involve changes to code within the project repository

Before the "In progress" step is complete, the developer should open a new Pull Request documenting the changes that
have been made.

It is ok to have one Pull Request that covers multiple subtasks. Just be sure to add all the appropriate links.

If the project workflow includes a [Design review](#design-review) step, then this Pull Request should be marked as a
"WIP" (Work In Progress). We mark it as "WIP" because the work is not yet ready for another developer to review, but it
is still incredibly important to document the work completed to date - in case (for whatever reason) the original
developer is unable to see the work through to completion. 

### Once complete

Move your subtasks over to [Design review](#design-review) (if applicable), or [Peer review](#peer-review).

## Design review

This step is relevant when:

* Frontend work is part of the delivery of the subtask/s
* A designer is working with the team on delivery of the project

The purpose of a design review is to find and fix any inconsistencies between the design and the implementation in an
environment that allows for faster iteration. This step also gives the designer a chance to check that functionality
and interactions are working as they expected.

**Always remember:** We all have our own deadlines. You should expect a design review to take some time - you cannot
expect it to be completely immediately (the same goes for [Peer review](#peer-review)).

**Always remember:** You are not your code.

### What a design review isn't

A design review is **not** a browser test. The designer will review your implementation in a single browser, and they
will provide feedback based on that. It is still up to you to make sure that every other supported browser matches
their ACs.

### How to request a design review

To request a design review, you will need to make your work available to the designer in some way. Below are some
options that you could use.

#### Best options

In person. If you're in the same office as the designer, organise a time to sit down with them and go through your
implementation.

*Or*

Provide a public URL for the designer to access your local environment. Tools such as
[ngrok](https://ngrok.com/download) can be used to achieve this. If you need to continue developing in the meantime,
you can switch to another branch, or you might want to consider spinning up a second local environment that you can use
exclusively for requesting design reviews.

#### Good option

If you have an available Cloud/CWP environment that you can deploy your branch to, then this can be an option that will
allow the designer to view your work "at any time".

The difficulty here is that someone else might end up needing that environment for something, so this option is not
always the best option.

#### Last resort

As a last resort, if none of the above options are achievable (for whatever reason/s) within the time frame required,
you can instead provide screenshots of your implementation.

Be absolutely sure to take screenshots of all interactions and at all breakpoint sizes.

### What to do with the design feedback

Some feedback that you get will be pretty cut and dry. EG: There was something defined in the designs that you missed
or got incorrect (font size, spacing, interactions, etc). These types of feedback usually do not require any further
discussion, and they can be implemented immediately, followed by another round of design review.

Some times you might get feedback that is less clear cut. This could include discovering some edge cases that were not
originally considered, or it might include design changes. Never dismiss any feedback given, however, if you feel that
the work required to complete the feedback now puts your sprint at risk, you could consider splitting out this feedback
as an "improvement" or "clarification" subtask. It can be a good idea to raise your question in the project channel, or
in the next standup. Ultimately, the product owner is in charge of deciding how to spend their budget - if it has been
determined that (for whatever reason) a feature is now larger than initially sized/scoped, it is important that they
are across it, and that they accept it. Sometimes we get to implement everything. Other times we need to consider MVP
and treat anything else as an "improvement".

### Once complete

Move your subtasks over to [Peer review](#peer-review).

## Peer review

Make sure your Pull Request is up to date and ready for another developer to review.

Be sure to add a link to your Pull Request in the Jira subtask/s, and likewise add link/s to the Jira subtask/s in your
Pull Request.

**Always remember:** You are not your code.

### How to give a good peer review

Check out Google's thoughts on Peer Review:

https://google.github.io/eng-practices/review/reviewer/

### What to do with the peer review feedback

We mostly treat this feedback the same as we did from [Design review](#what-to-do-with-the-design-feedback), but of
course the focus is now going to include code quality, and it might also include things like testing CMS functionality
(which [Design review](#design-review) may not have).

The same deal applies here though: If, through [Peer review](#peer-review), the team determines that the size/scope of
the feature has increased beyond initial estimates, it can be a good idea to raise "improvement" or "clarification"
subtasks so that the product owner (and the rest of the team) are aware. Sometimes we get to implement everything. Other
times we need to consider MVP and treat anything else as an "improvement".

### Once complete

Merge the pull request.

Move your subtasks over to [Awaiting deployment](#awaiting-deployment).

## Awaiting deployment

In order for the product owner (and/or testers) to test your work, it needs to be up on an environment for them. This
column just represents the fact that the work has been merged, but it is not yet available on an environment for
testing. 

Anyone in the development team should be able to perform a deployment to the appropriate environment.

### Once deployment completes

Move any subtask/s that are in [Awaiting deployment](#awaiting-deployment) to [Dev testing](#dev-testing).

## Dev testing

A sanity check to make sure that the merge and deployment didn't create any regressions.

If you find any issues (including regressions), you should raise new subtasks in the appropriate stories to cover the
fixing of those issues. We create new subtasks because you will likely need to go through another round of
[Design review](#design-review) and [Peer review](#peer-review).

### Once complete

Move the appropriate subtask/s to "Client testing". Some teams use the original subtask for
[Client testing](#client-testing), others use a separate "Client testing" subtask.

Move the appropriate subtask/s to "Done". EG: Any regressions subtasks that the team found and raised before any client
testing began can just be closed, they do not need to be moved to [Client testing](#client-testing).

## Client testing

The product owner (and/or testers) will now run through their own testing.

Any issues that are found should be raised as new sub bugs within the story for someone in the team to pick up.

Similar to [Design review](#design-review) and [Peer review](#peer-review), if new subtasks are raised that threaten the
sprint, you should discuss whether they are required for MVP or not.

## Done

Task complete! This task should never be reopened (unless it was closed by mistake). If new things are discovered, they
should be raised as new subtasks or perhaps even as entirely new stories in the backlog.
