---
layout: article
date: 2009-04-26
permalink: innovations/test-planning
label: Bugs Planning
title: "Planning Of Software Bugs"
intro: "Learn how we plan software bugs to improve the overal quality of product"
tags: innovations
description: |
  One of the most common mistake in software development is a
  negative attitute towards bugs. In the article we explain
  that software bugs should be planned the same way as you
  plan new features and releases of your product.
keywords:
  - test planning
  - software testing
  - bugs planning
  - software defects
  - software bugs
  - defective software
  - software quality
  - quality control
next_step: innovations/metrics
---

You won't build a quality software product if your mindset is as follows:

* I release the product when quality testers give the okay
* The software quality is validated by testers
* High quality means complete lack of bugs

The above statements are common for
testers, programmers and project sponsors.
During the last 40 years, software books have told us that
the assumptions above are the biggest software problems themselves, but
software teams still operate under these assumptions.

## Iterative Testing is Different, Upside Down

First, there are a number of critical axioms that you as
a project sponsor or project manager must remember:

**Axiom 1**: Any software product has an unlimited
number of bugs not discovered yet

In other words you can't control number of bugs
in the software they are unlimited. You can only control the
number of bugs you discover, which is dependant on the limit of your time and resources.

**Axiom 2**: Quality is the degree to which
a set of characteristics fulfills requirements

Quality shows how *good* your product is compared to
your initial expectations. In combining these two axioms we can say
that the software product will be "better" when you
find and fix more bugs.
The characteristics are the number of fixed bugs, while
requirements are the number of bugs anticipated.

Formally, Quality of Implementation (**QoI**) is a relation between
the number of fixed bugs (**F**) and number of bugs anticipated (**P**):

{% tikz  %}
\begin{center}
\begin{equation*}
\displaystyle
    QoI = \frac{F}{P}
\end{equation*}
\end{center}
{% endtikz %}

Quality of Product (**QoP**) is a relationship between the number
of bugs anticipated to be discovered (**P**) and the product size (**S**)
multiplied by a nominal number of bugs per size unit (**k**).

{% tikz %}
\begin{center}
\begin{equation*}
\displaystyle
    QoP = \frac{P}{S \times k}
\end{equation*}
\end{center}
{% endtikz %}

The more bugs you anticipate you will discover, the larger
your testing budget, the more time you need, and the higher you Quality of Product.

When you say "*I want to create a quality product*" it means that
you are planning to develop the software and to find/fix more
bugs than usual leading to a QoP higher than usual. For example
the product specification will have 100 functional requirements.
Usually, your testers find 5 bugs per one functional requirement,
spending 0.5 man-hours per one bug (250 totall man-hours).
"Software of higher quality" will mean, for example, 10 bugs per one requirement, and 1000
bugs in the entire product. This will require additional budget and may
increase the average cost of one bug, this will honestly
mean that you're planning to create "a product of higher quality".

When you say "*the software is properly tested*" it means that
your testers discovered all the bugs you anticipated discovering
and that programmers fixed the majority of them. If you anticipated
finding 1000 bugs, and you found 900 while 100 of them remained
unfixed, then the Quality of Implementation is 80\%.

Now let's review the assumptions listed above.

**I release the product when my quality testers give the okay** (wrong).
Testers are not performing quality control, they are discovering bugs.
Testers don't know how this process affects your
judgment about product release. You release the product to the
market when you think the QoI/QoP is sufficient.

**The software quality is validated by testers** (wrong).
The testers's job is to discover bugs. They don't control
the QoI or QoP &mdash; a project manager plans Quality of Product (QoP)
and programmers reach Quality of Implementation (QoI). Testers
create inputs (bugs) for programmers.

**High quality means a complete lack of bugs** (wrong).
The more bugs you are anticipating to discover,
the higher the Quality of Product you are going to achieve. The more bugs you
discover and fix, the higher the Quality of Implementation.
When the team reports to you that there have been no bugs found,
you know that the project has been
poorly planned and testing poorly performed.

## How to Estimate Number of Bugs?

The most common question is *how can we estimate
the number of bugs to be found in the product?*
The answers are expert judgment, historical information,
and prototyping.

Remember, that you may always change this number during
the course of project. After all, we do this iteratively, don't we? You
anticipate the number of bugs according to your own judgment. It's
good to start with something like (this is **k** in the formula above):

 * bugs per line of code
 * bugs per functional requirement
 * bugs per class/method/module
 * bugs per web page

You can create your own metrics. Your first priority is to anticipate
the number of bugs and your second priority is to anticipate it correctly.

## How to Estimate Discovery Time for Bugs?

Do this in the same manner in which your programmers estimate their time.
Testing is the same process as programming
and any other engineering task.

Before the next iteration quality tester receives the SRS,
the list of functional requirements to be tested in a particular
iteration, the required numbers of bugs to be detected should be decided and
a time estimate &mdash; how much time will it take him/her to discover the required number of bugs
&mdash; to be produced.

If the answer is *I don't know how many bugs this functionality has*,
refer him/her to Axiom 1.

## Conclusion or What Do You Do Next

Bugs have to be anticipated the same way as you plan new
features and new releases. You have to plan your quality
and control it during the entire project. Your plan may
fall wide of the mark in the beginning of the project,
you may underestimate testing efforts,
and your important assumptions may be incorrect.
But the biggest mistake you could make is not to anticipate bugs at all.

## Bibliography

Brooks, Frederick P.,
The mythical man-month: essays on software engineering,
Addison Wesley Longman, Inc., USA (1995)

Kaner Cem, Falk Jack, Nguyen Hung Quoc,
Testing computer software, Second Edition,
John Wiley and Sons, Inc, New York, USA (1999)

Nguyen Hung Quoc, Johnson Bob, Hackett Michael,
Testing applications on the Web, Second Edition,
John Wiley and Sons, New York, USA (2003)

Myers, Glenford J.,
The art of software testing, 2nd edition,
John Wiley and Sons, New Jersey, USA (2004)

A Guide to the Project Management Body of Knowledge (PMBOK Guide), Third Edition (2004)
Project Management Institute (PMI),
PMI Press, USA (2004)
