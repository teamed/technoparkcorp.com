---
layout: article
date: 2014-08-13
permalink: about/news/2014/rultor-automated-merge-assistant
tags: news
label: rultor
title: "Rultor.com Automates Routine DevOps Operations"
description: |
  Rultor.com was recently launched, to automate
  routine DevOps operations, including merging, deploying and releasing
keywords:
  - devops
  - rultor
  - automated merge
  - github pull request
  - pre-flight build
  - continuous integration
---

[Rultor.com](http://www.rultor.com), a new cloud service for DevOps automation,
was launched a few weeks ago by TechnoPark Corp. It is a unique and the only
service that enables smooth integration of programming team with DevOps
processes, in an interactive way.

Right after its official launch in July 2014, [Rultor](http://www.rultor.com)
started to serve over 50 open source projects in Github, including
[Jcabi](http://www.jcabi.com), [Qulice](http://www.qulice.com),
and [ReXSL](http://www.rexsl.com). Moreover, it automates its own
DevOps operations, releasing new versions of itself to [CloudBees](http://www.cloudbees.com)
(this is where its core module is hosted).

Yegor Bugayenko, chief architect of [Rultor](http://www.rultor.com), claims
that "the service is highly scalable and flexible, since it uses
Amazon Web Services EC2 platform for server machines, and runs every
build in its own Docker container".

The use of Docker is one of the most interesting features of
[Rultor](http://www.rultor.com). Indeed, the technology enables
a perfecly safe isolation of running builds from different projects.
Every project in every build has its own Docker container, that can
be configured individually, and gets deleted right after the build
is finished. This use of Docker makes [Rultor](http://www.rultor.com)
very unique on the market of continuous integration hosted solutions.

Another unique feature of [Rultor](http://www.rultor.com) is that
it doesn't have a management panel. Instead, it communicates
with programming team through their existing issue tracking system.
At the moment, Github issue tracking is the only system [Rultor](http://www.rultor.com)
supports. In the nearest future, JIRA, Trac, and Basecamp will
be supported.

TechnoPark Corp. fully sponsors the project, that's why
[Rultor](http://www.rultor.com) is absolutely free both for
open source and commercial projects.
