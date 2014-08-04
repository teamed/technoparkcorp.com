---
layout: article
date: 2014-08-08
permalink: process/scope/changerequest
label: Change Request
term: requestedchanges
title: "Changes Happen And We Manage Them"
intro: "Learn about change management procedures"
description: |
  Change control procedure is a very important component of
  scope management. Change Request is the only document that
  can make changes to the baselined SRS.
keywords:
  - change request
  - SRS
  - software requirements specification
  - change control
  - software changes tracking
  - continuous integration
  - software outsourcing
  - offshore development
  - distributed programming
next_step: process/time
---

Changes are inevitable in software development projects and we recognize that change and the
resulting uncertainty are inherent aspects of the IT life cycle. A change has potential to ruin the
project, or it can creatively increase the value of the business. The outcome depends on an
established change control procedure created during the project.

We advocate a strict approach to change management. Each new change is formally reviewed, estimated,
and approved prior to being implemented.

<tikz>
\begin{tikzpicture}
    [node distance = 2.5cm,
    auto, thick]

    \node [block, text width=2.5cm] (request) {Request Change};
    \node [block, below of=request] (impact) {Impact};
    \node [choice, below of=impact, aspect=1.5] (accept) {Accept impact?};
    \node [block, text width=3.5cm, below of=accept] (new) {New Specification};
    \node [block, text width=5cm, below of=new] (changes) {Changes to Schedule, Risk and Budget};
    \node [choice, below of=changes, aspect=1.5] (approve) {Approve changes?};
    \node [block, text width=7cm, below of=approve] (versions) {New Versions of Schedule, Risk, Budget, and Specification};

    \path [line] (request) -- node [right, text width=7cm] {Impact analysis by project manager, system analyst and project team} (impact);
    \path [line] (impact) -- (accept);
    \path [line] (accept) --  node[near start] {yes} (new);
    \path [line] (new) -- (changes);
    \path [line] (changes) -- (approve);
    \path [line] (approve) -- node[near start] {yes} (versions);

    \draw [line] (accept) -- node[near start] {no} +(0:4cm) node[right] {No changes};
    \draw [line] (approve) -- node[near start] {no} +(0:4cm) node[right, text width=4cm] {Rollback to existing Baseline};
\end{tikzpicture}
    </tikz>

There are several consecutive steps in a change control procedure:

<ol>
        <li>Requested change comes from you or project team (in any form).</li>

        <li>Project manager, with the help of system analyst and project team, analyzes the impact.
        The impact is estimated in staff-hours ([Cost](/process/cost)) and business days
        ([Schedule](/process/time/schedule)).</li>

        <li>You approve or reject the estimation.</li>

        <li>New [Specification](/process/scope/specification) is developed by the project team.</li>

        <li>Changes to Schedule, [Risks](/process/risk),
        and [Budget](/process/cost/budget) and
        are estimated and presented.</li>

        <li>You approve or reject the changes.</li>
    </ol>

When the above procedure is finished, the changes become effective and the project team works with
the new Schedule, Budget and Risks.
