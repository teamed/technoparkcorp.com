---
layout: article
date: 2010-07-21
permalink: about/news/year2010/pdd-patent
tags: news
label: PDD Patent
title: "Pdd Patent Application No. 12/840,306"
description: |
  Patent for Puzzle Driven Development (PDD) mechanism that
  helps distributed software development teams keep changes
  under control has been submitted to USPTO.
keywords:
  - puzzle driven development
  - distributed programming
  - remote team
  - quality control
  - continuous integration
---

Patent application
[no. 12/840,306](https://www.google.com/patents/US20120023476)
has been submitted to the United States Patent and Trademark Office on
21st of July by Yegor Bugayenko, lead architect of TechnoPark Corp.

The invention includes a specific method and software that resolves the problem of "delayed
branches" conflict in concurrent distributed software development and in many other types of
software development projects.

Every time a developer is working with a branch and sees a problem
or a question that needs the participation of another programmer,
he implements a temporary solution that keeps the code compilable.
He marks the place in the code with `@todo`
tag (called "**puzzle**") and merges
the branch into `trunk`. The "**puzzle**"
includes the identifier of a task he was working with. As long as the
"**puzzle**" stays in source code, a project manager considers the task as
incomplete and pending resolution. The project manager assigns "**puzzle**" resolution
to other team members. When all "**puzzles**" are resolved, the project manager
returns the task back to the programmer, asking him to continue with development.

The key advantage of the PDD method, comparing with all other known approaches,
is the absence of long branches. Implementation of every task, no matter
how difficult it is, takes a few hours in one iteration. Then the task
is set to pending state and new "**puzzles**" are merged into `trunk`. Project
planning becomes more predictable and simple since the project manager is
dealing with a large amount of small, isolated tasks, instead of long and risky
activities. With this method, cost and scope control also becomes more effective.

Properly used "**puzzles**" become the main management and communication
mechanism in a distributed software project, replacing e-mails, online
discussions, and phone calls. Moreover; the PDD software
collects "**puzzles**" from source code and builds short-term
plans of key development tasks.
